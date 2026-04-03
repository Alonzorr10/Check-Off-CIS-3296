import { initializeApp } from "firebase/app";
import {
    getFirestore,
    updateDoc,
    collection,
    addDoc,
    query,
    where,
    deleteDoc,
    doc,
    getDoc,
    onSnapshot,
    serverTimestamp,
    Timestamp,
} from "firebase/firestore";
import { getAuth, onAuthStateChanged } from "firebase/auth";
import {
    incrementSettlementStreak,
    normalizeSettlementUserKey,
    resetSettlementStreak,
} from "./settlement-streak";

const firebaseConfig = {
    apiKey: import.meta.env.VITE_FIREBASE_API_KEY || "",
    authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN || "",
    projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID || "",
    storageBucket: import.meta.env.VITE_FIREBASE_STORAGE_BUCKET || "",
    messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID || "",
    appId: import.meta.env.VITE_FIREBASE_APP_ID || "",
};

const app = initializeApp(firebaseConfig);
const db = getFirestore(app);
const auth = getAuth(app);

let currentUserId = null;
let currentUserName = null;
let currentUserEmail = null;
let contributionUnsubscribes = [];

// --- UI Helpers ---

window.toggleForm = (id) => {
    const form = document.getElementById(`form-${id}`);
    if (form) {
        form.classList.toggle("hidden");
    }
};

window.toggleEmailField = (eventId, show) => {
    const container = document.getElementById(`email-container-${eventId}`);
    const emailInput = document.getElementById(`owner-email-${eventId}`);
    if (show) {
        container.classList.remove("hidden");
        emailInput.focus();
    } else {
        container.classList.add("hidden");
        emailInput.value = "";
    }
};

// --- Event Management ---

window.addNewEventBlock = function () {
    const container = document.getElementById("event-container");
    if (!container) return;

    const tempId = Date.now();
    const randomCode = Math.random().toString(36).substring(2, 8).toUpperCase();

    const html = `
        <div class="bg-stone-900 border-2 border-emerald-500 rounded-2xl p-6 mb-4 shadow-2xl" id="temp-${tempId}">
            <input type="text" id="input-name-${tempId}"
                   class="w-full p-3 bg-stone-950 border-stone-700 rounded-xl text-white mb-4 outline-none focus:border-emerald-500"
                   placeholder="Enter Event Name">
            <div class="flex gap-3">
                <button type="button" onclick="confirmNewEvent('${tempId}', '${randomCode}')"
                        class="bg-emerald-600 hover:bg-emerald-500 text-white px-6 py-2 rounded-xl text-sm font-bold transition">
                    Save Event
                </button>
                <button type="button" onclick="document.getElementById('temp-${tempId}').remove()"
                        class="text-stone-400 text-sm hover:text-white transition">
                    Cancel
                </button>
            </div>
        </div>`;

    container.insertAdjacentHTML("afterbegin", html);
    setTimeout(
        () => document.getElementById(`input-name-${tempId}`).focus(),
        10,
    );
};

window.confirmNewEvent = async function (tempId, code) {
    const nameInput = document.getElementById(`input-name-${tempId}`);
    if (!nameInput || !nameInput.value.trim())
        return alert("Enter an event name.");

    try {
        await addDoc(collection(db, "events"), {
            name: nameInput.value.trim(),
            code: code,
            creator_id: currentUserId,
            creator_name: currentUserName,
            creator_email: currentUserEmail,
            created_at: serverTimestamp(),
        });
        document.getElementById(`temp-${tempId}`).remove();
    } catch (error) {
        console.error("Error saving event:", error);
    }
};

window.deleteEvent = async (id) => {
    if (confirm("Delete this event and all associated items permanently?")) {
        try {
            await deleteDoc(doc(db, "events", id));
        } catch (error) {
            console.error("Error deleting event:", error);
        }
    }
};

// --- Auth & Listeners ---

onAuthStateChanged(auth, (user) => {
    if (user) {
        currentUserId = user.uid;
        currentUserEmail = user.email;
        currentUserName = user.displayName || "Anonymous";
        if (window.eventListenerUnsubscribe) window.eventListenerUnsubscribe();
        listenToEvents();
    } else {
        window.location.href = "/login";
    }
});

function listenToEvents() {
    const q = query(
        collection(db, "events"),
        where("creator_id", "==", currentUserId),
    );
    window.eventListenerUnsubscribe = onSnapshot(q, (snapshot) => {
        contributionUnsubscribes.forEach((unsub) => unsub());
        contributionUnsubscribes = [];
        renderUI(snapshot);
    });
}

function formatDate(timestamp) {
    if (!timestamp?.toDate) return "No due date";
    return timestamp.toDate().toLocaleString();
}

// --- Rendering ---

function renderUI(snapshot) {
    const container = document.getElementById("event-container");
    if (!container) return;
    container.innerHTML = "";

    snapshot.docs.forEach((eventDoc) => {
        const event = eventDoc.data();
        const eventId = eventDoc.id;
        const listId = `items-list-${eventId}`;

        const eventHtml = `
            <div class="bg-stone-900 border border-stone-800 rounded-2xl p-6 mb-4 shadow-xl" id="block-${eventId}">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-white">${event.name}</h3>
                        <span class="text-[11px] text-emerald-500 font-mono tracking-widest">CODE: ${event.code}</span>
                    </div>
                    <button onclick="deleteEvent('${eventId}')" class="text-stone-500 hover:text-red-500 text-xs">Delete Event</button>
                </div>

                <div id="${listId}" class="space-y-1 mb-4 min-h-[20px]">
                    <div class="text-[10px] text-stone-600 italic">Syncing items...</div>
                </div>

                <div id="form-${eventId}" class="hidden bg-stone-950 p-4 rounded-xl mb-4 border border-stone-800">
                    <input type="text" id="label-${eventId}" placeholder="Item label" class="text-sm w-full p-2 mb-3 bg-stone-900 border-stone-700 rounded text-white outline-none">

                    <div class="flex gap-4 mb-3 px-1">
                        <label class="flex items-center gap-2 text-[10px] text-stone-400 cursor-pointer">
                            <input type="radio" name="debtor-type-${eventId}" value="guest" checked onclick="toggleEmailField('${eventId}', false)" class="accent-emerald-500"> Guest
                        </label>
                        <label class="flex items-center gap-2 text-[10px] text-stone-400 cursor-pointer">
                            <input type="radio" name="debtor-type-${eventId}" value="user" onclick="toggleEmailField('${eventId}', true)" class="accent-emerald-500"> Registered User
                        </label>
                    </div>

                    <div class="flex gap-2 mb-3">
                        <input type="text" id="owner-${eventId}" placeholder="Debtor name" class="text-sm w-full p-2 bg-stone-900 border-stone-700 rounded text-white outline-none">
                        <input type="number" id="amount-${eventId}" placeholder="¥" class="text-sm w-24 p-2 bg-stone-900 border-stone-700 rounded text-white outline-none">
                    </div>

                    <div id="email-container-${eventId}" class="hidden mb-3">
                        <input type="email" id="owner-email-${eventId}" placeholder="Debtor email" class="text-sm w-full p-2 bg-stone-900 border-emerald-900/50 border rounded text-white outline-none">
                    </div>

                    <div class="mb-4">
                        <label class="text-[9px] text-stone-500 uppercase font-bold ml-1">Due Date</label>
                        <input type="datetime-local" id="due-at-${eventId}" class="text-sm w-full p-2 bg-stone-900 border-stone-700 rounded text-white outline-none">
                    </div>

                    <div class="flex gap-2">
                        <button type="button" onclick="saveContribution('${eventId}', '${event.code}')" class="bg-emerald-600 text-white px-4 py-1.5 rounded-lg text-xs font-bold">Add Item</button>
                        <button type="button" onclick="toggleForm('${eventId}')" class="text-stone-400 text-xs">Cancel</button>
                    </div>
                </div>

                <button onclick="toggleForm('${eventId}')" class="w-full py-2 border border-dashed border-stone-700 rounded-xl text-xs text-stone-500 hover:bg-stone-800 transition">
                    + Add Contribution
                </button>
            </div>`;

        container.insertAdjacentHTML("beforeend", eventHtml);

        const contQ = query(
            collection(db, "contributions"),
            where("event_code", "==", event.code),
        );
        const unsub = onSnapshot(contQ, (contSnap) => {
            const listDiv = document.getElementById(listId);
            if (!listDiv) return;

            let itemsHtml = "";
            let total = 0;

            contSnap.forEach((cDoc) => {
                const item = cDoc.data();
                const amount = parseFloat(item.amount) || 0;
                total += amount;

                const status = item.status || "pending";
                const isVerifying = status === "pending_verification";
                const isSettled = status === "settled";

                itemsHtml += `
                    <div class="py-3 border-b border-stone-800 last:border-0">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="font-bold text-white text-[13px]">${item.label}</div>
                                <div class="text-[11px] text-stone-500">${item.debtor_name} (${item.email || "Guest"})</div>
                                <div class="text-[10px] text-stone-600">Due: ${formatDate(item.due_at)}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-[13px] text-white font-medium ${isSettled ? "text-white" : "text-emerald-400"}">¥${amount.toLocaleString()}</div>
                                <span class="text-[9px] px-2 py-0.5 rounded-full uppercase ${isVerifying ? "bg-amber-500 text-white animate-pulse" : "bg-stone-800 text-stone-500"}">${status.replace("_", " ")}</span>
                            </div>
                        </div>
                        ${
                            isVerifying
                                ? `
                            <div class="mt-3 flex gap-2">
                                <button onclick="verifyPayment('${cDoc.id}', 'settled')" class="flex-1 bg-emerald-600 text-white text-[10px] py-1.5 rounded-md font-bold">Confirm</button>
                                <button onclick="verifyPayment('${cDoc.id}', 'denied')" class="flex-1 bg-stone-800 text-stone-300 text-[10px] py-1.5 rounded-md">Deny</button>
                            </div>`
                                : ""
                        }
                    </div>`;
            });

            listDiv.innerHTML =
                itemsHtml +
                (total > 0
                    ? `<div class="text-right font-bold text-white mt-3 text-sm">Total: ¥${total.toLocaleString()}</div>`
                    : "");
        });
        contributionUnsubscribes.push(unsub);
    });
}

// --- Core Actions ---

window.saveContribution = async function (eventId, eventCode) {
    const type = document.querySelector(
        `input[name="debtor-type-${eventId}"]:checked`,
    ).value;
    const isUser = type === "user";

    const label = document.getElementById(`label-${eventId}`).value.trim();
    const debtor = document.getElementById(`owner-${eventId}`).value.trim();
    const email = document
        .getElementById(`owner-email-${eventId}`)
        .value.trim();
    const amount = document.getElementById(`amount-${eventId}`).value.trim();
    const dueAt = document.getElementById(`due-at-${eventId}`).value;

    if (!label || !debtor || !amount || !dueAt)
        return alert("Fill all required fields.");
    if (isUser && !email) return alert("Registered users require an email.");

    try {
        await addDoc(collection(db, "contributions"), {
            event_code: eventCode,
            label,
            debtor_name: debtor,
            email: isUser ? normalizeSettlementUserKey(email) : null, // Used for the search page
            debtor_email: email ? normalizeSettlementUserKey(email) : null, // Used for streak logic
            debtor_type: type,
            amount: parseFloat(amount),
            due_at: Timestamp.fromDate(new Date(dueAt)),
            status: "pending",
            created_at: serverTimestamp(),
        });
        window.toggleForm(eventId);
    } catch (error) {
        console.error("Error adding contribution:", error);
    }
};

window.verifyPayment = async function (docId, newStatus) {
    if (
        !confirm(
            `Are you sure you want to ${newStatus.toUpperCase()} this payment?`,
        )
    )
        return;

    try {
        const docRef = doc(db, "contributions", docId);
        const snap = await getDoc(docRef);
        if (!snap.exists()) return;

        const data = snap.data();
        const updateData = {
            status: newStatus,
            reviewed_at: serverTimestamp(),
        };

        if (newStatus === "settled") {
            const dueAt = data.due_at?.toDate();
            const paidAt = data.paid_at?.toDate();
            if (dueAt && paidAt && paidAt <= dueAt) {
                await incrementSettlementStreak(db, {
                    userEmail: data.debtor_email,
                    contributionId: docId,
                });
            } else {
                await resetSettlementStreak(db, {
                    userEmail: data.debtor_email,
                    contributionId: docId,
                    reason: "late",
                });
            }
        }
        await updateDoc(docRef, updateData);
    } catch (e) {
        console.error(e);
    }
};
