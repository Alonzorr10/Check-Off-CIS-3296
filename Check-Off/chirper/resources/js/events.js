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

window.toggleForm = (id) => {
    const form = document.getElementById(`form-${id}`);
    if (form) {
        form.classList.toggle("hidden");
    }
};

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

window.deleteEvent = async (id) => {
    if (confirm("Delete this event and all associated items permanently?")) {
        try {
            await deleteDoc(doc(db, "events", id));
        } catch (error) {
            console.error("Error deleting event:", error);
        }
    }
};

onAuthStateChanged(auth, (user) => {
    if (user) {
        currentUserId = user.uid;
        currentUserEmail = user.email;
        currentUserName = user.displayName || "Anonymous";

        if (window.eventListenerUnsubscribe) {
            window.eventListenerUnsubscribe();
        }
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
                    <input type="text" id="label-${eventId}" placeholder="Item label" class="text-sm w-full p-2 mb-2 bg-stone-900 border-stone-700 rounded text-white outline-none">
                    <input type="text" id="owner-${eventId}" placeholder="Debtor name" class="text-sm w-full p-2 mb-2 bg-stone-900 border-stone-700 rounded text-white outline-none">
                    <input type="email" id="owner-email-${eventId}" placeholder="Debtor email" class="text-sm w-full p-2 mb-2 bg-stone-900 border-stone-700 rounded text-white outline-none">
                    <div class="flex gap-2 mb-2">
                        <input type="number" id="amount-${eventId}" placeholder="Amount" class="text-sm w-full p-2 bg-stone-900 border-stone-700 rounded text-white outline-none">
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
                const isDenied = status === "denied";

                itemsHtml += `
                <div class="py-3 border-b border-stone-800 last:border-0">
                    <div class="flex justify-between items-start gap-4">
                        <div class="text-[13px] text-stone-300">
                            <div class="font-bold text-white">${item.label}</div>
                            <div class="text-[11px] text-stone-500">${item.debtor_name}</div>
                            <div class="text-[11px] text-stone-500">${item.debtor_email || "No email set"}</div>
                            <div class="text-[10px] text-stone-600 mt-1">Due: ${formatDate(item.due_at)}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-[13px] font-medium ${isSettled ? "text-stone-500" : "text-emerald-400"}">
                                ¥${amount.toLocaleString()}
                            </div>
                            <span class="text-[9px] px-2 py-0.5 rounded-full uppercase tracking-tighter
                                ${
                                    isSettled
                                        ? "bg-emerald-900/30 text-emerald-500"
                                        : isDenied
                                          ? "bg-red-900/30 text-red-400"
                                          : isVerifying
                                            ? "bg-amber-500 text-black font-bold animate-pulse"
                                            : "bg-stone-800 text-stone-500"
                                }">
                                ${status.replace("_", " ")}
                            </span>
                        </div>
                    </div>

                    ${
                        item.payment_note
                            ? `<div class="mt-2 text-[10px] text-stone-400 italic">Payment note: ${item.payment_note}</div>`
                            : ""
                    }

                    ${
                        isVerifying
                            ? `
                        <div class="mt-3 bg-stone-950 p-3 rounded-lg border border-amber-900/50">
                            <div class="text-[10px] text-amber-200 mb-2 italic">Review this submitted payment</div>
                            <div class="flex gap-2">
                                <button onclick="verifyPayment('${cDoc.id}', 'settled')" class="flex-1 bg-emerald-600 text-white text-[10px] py-1.5 rounded-md font-bold">Confirm</button>
                                <button onclick="verifyPayment('${cDoc.id}', 'denied')" class="flex-1 bg-stone-800 text-stone-300 text-[10px] py-1.5 rounded-md">Deny</button>
                            </div>
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

window.saveContribution = async function (eventId, eventCode) {
    const labelInput = document.getElementById(`label-${eventId}`);
    const debtorInput = document.getElementById(`owner-${eventId}`);
    const debtorEmailInput = document.getElementById(`owner-email-${eventId}`);
    const amountInput = document.getElementById(`amount-${eventId}`);
    const dueAtInput = document.getElementById(`due-at-${eventId}`);

    const label = labelInput.value.trim();
    const debtor = debtorInput.value.trim();
    const debtorEmail = normalizeSettlementUserKey(debtorEmailInput.value);
    const amount = amountInput.value.trim();
    const dueAtValue = dueAtInput.value;

    if (!label || !debtor || !debtorEmail || !amount || !dueAtValue) {
        return alert("Please fill out label, debtor name, debtor email, amount, and due date.");
    }

    try {
        await addDoc(collection(db, "contributions"), {
            event_code: eventCode,
            label,
            debtor_name: debtor,
            debtor_email: debtorEmail,
            amount: parseFloat(amount),
            due_at: Timestamp.fromDate(new Date(dueAtValue)),
            status: "pending",
            payment_note: "",
            created_at: serverTimestamp(),
        });

        labelInput.value = "";
        debtorInput.value = "";
        debtorEmailInput.value = "";
        amountInput.value = "";
        dueAtInput.value = "";
        window.toggleForm(eventId);
    } catch (error) {
        console.error("Error adding contribution", error);
        alert("Failed to add contribution.");
    }
};

window.confirmNewEvent = async function (tempId, code) {
    const nameInput = document.getElementById(`input-name-${tempId}`);
    if (!nameInput) return;

    const name = nameInput.value.trim();
    if (!name) {
        alert("Please enter a name for your event.");
        return;
    }

    if (!currentUserId) {
        alert("Authentication error. Please refresh and log in again.");
        return;
    }

    try {
        await addDoc(collection(db, "events"), {
            name,
            code,
            creator_id: currentUserId,
            creator_name: currentUserName,
            creator_email: currentUserEmail,
            created_at: serverTimestamp(),
        });

        const tempBlock = document.getElementById(`temp-${tempId}`);
        if (tempBlock) tempBlock.remove();
    } catch (error) {
        console.error("Error saving event to Firestore:", error);
        alert("Failed to save event.");
    }
};

window.verifyPayment = async function (docId, newStatus) {
    const action = newStatus === "settled" ? "CONFIRM" : "DENY";
    if (!confirm(`Are you sure you want to ${action} this payment?`)) return;

    try {
        const docRef = doc(db, "contributions", docId);
        const contributionSnap = await getDoc(docRef);

        if (!contributionSnap.exists()) {
            alert("Contribution not found.");
            return;
        }

        const contribution = contributionSnap.data();
        const debtorEmail = normalizeSettlementUserKey(contribution.debtor_email);
        const dueAt = contribution.due_at?.toDate ? contribution.due_at.toDate() : null;
        const paidAt = contribution.paid_at?.toDate ? contribution.paid_at.toDate() : null;

        const updateData = {
            status: newStatus,
            reviewed_at: serverTimestamp(),
            reviewed_by: currentUserEmail || null,
        };

        if (newStatus === "denied") {
            await resetSettlementStreak(db, {
                userEmail: debtorEmail,
                contributionId: docId,
                eventCode: contribution.event_code || null,
                reason: "denied",
            });
        }

        if (newStatus === "settled") {
            updateData.settled_at = serverTimestamp();

            const isOnTime = !!(dueAt && paidAt && paidAt <= dueAt);

            if (isOnTime) {
                await incrementSettlementStreak(db, {
                    userEmail: debtorEmail,
                    contributionId: docId,
                    eventCode: contribution.event_code || null,
                });
            } else {
                await resetSettlementStreak(db, {
                    userEmail: debtorEmail,
                    contributionId: docId,
                    eventCode: contribution.event_code || null,
                    reason: "late_confirmed",
                });
            }
        }

        await updateDoc(docRef, updateData);
    } catch (error) {
        console.error("Verification error:", error);
        alert("Failed to update payment status.");
    }
};

window.addNewEventBlock = addNewEventBlock;
window.confirmNewEvent = confirmNewEvent;
window.saveContribution = saveContribution;
window.toggleForm = toggleForm;
window.deleteEvent = deleteEvent;
window.verifyPayment = verifyPayment;