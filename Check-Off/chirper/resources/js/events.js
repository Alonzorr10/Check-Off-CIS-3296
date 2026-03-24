import { initializeApp } from "firebase/app";
import {
    getFirestore,
    updateDoc,
    collection,
    addDoc,
    getDocs,
    query,
    where,
    deleteDoc,
    doc,
    onSnapshot,
    serverTimestamp,
} from "firebase/firestore";
import { getAuth, onAuthStateChanged } from "firebase/auth";

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
                   placeholder="Enter Event Name (e.g. Birthday Dinner)">
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
            // The onSnapshot listener will automatically remove the UI block
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
                    <input type="text" id="label-${eventId}" placeholder="Item (e.g. Pizza)" class="text-sm w-full p-2 mb-2 bg-stone-900 border-stone-700 rounded text-white outline-none">
                    <div class="flex gap-2 mb-3">
                        <input type="text" id="owner-${eventId}" placeholder="Who owes?" class="text-sm w-full p-2 bg-stone-900 border-stone-700 rounded text-white outline-none">
                        <input type="number" id="amount-${eventId}" placeholder="¥" class="text-sm w-24 p-2 bg-stone-900 border-stone-700 rounded text-white outline-none">
                    </div>
                    <div class="flex gap-2">
                        <button type="button" onclick="saveContribution('${eventId}', '${event.code}')" class="bg-emerald-600 text-white px-4 py-1.5 rounded-lg text-xs font-bold">Add Item</button>
                        <button onclick="toggleForm('${eventId}')" class="text-stone-400 text-xs">Cancel</button>
                    </div>
                </div>

                <button onclick="toggleForm('${eventId}')" class="w-full py-2 border border-dashed border-stone-700 rounded-xl text-xs text-stone-500 hover:bg-stone-800 transition">
                    + Add Sub-Category
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

                const isVerifying = item.status === "pending_verification";
                const isSettled = item.status === "settled";

                itemsHtml += `
                <div class="py-3 border-b border-stone-800 last:border-0">
                    <div class="flex justify-between items-start">
                        <div class="text-[13px] text-stone-300">
                            <span class="font-bold text-white">${item.label}</span>
                            <div class="text-[11px] text-stone-500">${item.debtor_name}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-[13px] font-medium ${isSettled ? "text-stone-500" : "text-emerald-400"}">
                                ¥${amount.toLocaleString()}
                            </div>
                            <span class="text-[9px] px-2 py-0.5 rounded-full uppercase tracking-tighter
                                ${
                                    isSettled
                                        ? "bg-emerald-900/30 text-emerald-500"
                                        : isVerifying
                                          ? "bg-amber-500 text-black font-bold animate-pulse"
                                          : "bg-stone-800 text-stone-500"
                                }">
                                ${item.status.replace("_", " ")}
                            </span>
                        </div>
                    </div>
                    ${
                        isVerifying
                            ? `
                        <div class="mt-3 bg-stone-950 p-3 rounded-lg border border-amber-900/50">
                            <div class="text-[10px] text-amber-200 mb-2 italic">Note: "${item.payment_note || "No note"}"</div>
                            <div class="flex gap-2">
                                <button onclick="verifyPayment('${cDoc.id}', 'settled')" class="flex-1 bg-emerald-600 text-white text-[10px] py-1.5 rounded-md font-bold">Confirm</button>
                                <button onclick="verifyPayment('${cDoc.id}', 'pending')" class="flex-1 bg-stone-800 text-stone-400 text-[10px] py-1.5 rounded-md">Deny</button>
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
    const amountInput = document.getElementById(`amount-${eventId}`);

    const label = labelInput.value.trim();
    const debtor = debtorInput.value.trim();
    const amount = amountInput.value.trim();

    if (!label || !debtor || !amount) return alert("Fill out all fields!");

    try {
        await addDoc(collection(db, "contributions"), {
            event_code: eventCode,
            label: label,
            debtor_name: debtor,
            amount: parseFloat(amount),
            status: "pending",
            created_at: serverTimestamp(),
        });

        labelInput.value = "";
        debtorInput.value = "";
        amountInput.value = "";
        window.toggleForm(eventId);
    } catch (error) {
        console.error("Error adding contribution", error);
    }
};

window.confirmNewEvent = async function (tempId, code) {
    console.log("Attempting to save new event:", tempId);

    const nameInput = document.getElementById(`input-name-${tempId}`);
    if (!nameInput) {
        console.error("Could not find input for tempId:", tempId);
        return;
    }

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
        // Save the event to the 'events' collection
        const docRef = await addDoc(collection(db, "events"), {
            name: name,
            code: code,
            creator_id: currentUserId,
            creator_name: currentUserName,
            creator_email: currentUserEmail,
            created_at: serverTimestamp(),
        });

        console.log("Event saved successfully with ID:", docRef.id);

        // Remove the temporary 'Draft' block from the UI
        const tempBlock = document.getElementById(`temp-${tempId}`);
        if (tempBlock) tempBlock.remove();
    } catch (error) {
        console.error("Error saving event to Firestore:", error);
        alert("Failed to save event. Check your internet connection.");
    }
};

window.verifyPayment = async function (docId, newStatus) {
    const action = newStatus === "settled" ? "CONFIRM" : "DENY";
    if (!confirm(`Are you sure you want to ${action} this payment?`)) return;

    try {
        const docRef = doc(db, "contributions", docId);
        const updateData = { status: newStatus };
        if (newStatus === "pending") updateData.payment_note = "";

        await updateDoc(docRef, updateData);
    } catch (error) {
        console.error("Verification error:", error);
    }
};

window.addNewEventBlock = addNewEventBlock;
window.confirmNewEvent = confirmNewEvent;
window.saveContribution = saveContribution;
window.toggleForm = toggleForm;
window.deleteEvent = deleteEvent;
window.verifyPayment = verifyPayment;
