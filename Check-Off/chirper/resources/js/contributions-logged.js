import { initializeApp } from "firebase/app";
import { getAuth, onAuthStateChanged } from "firebase/auth";
import {
    getFirestore,
    collection,
    query,
    where,
    onSnapshot,
    doc,
    updateDoc,
    serverTimestamp,
} from "firebase/firestore";

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

let userEmail = null;

onAuthStateChanged(auth, (user) => {
    if (user) {
        userEmail = user.email;
        console.log("Logged in with", userEmail);
        startMemberListener(userEmail);
    } else {
        window.location.href = "/login";
    }
});

function startMemberListener(email) {
    const container = document.getElementById("user-contribution-container");

    const q = query(
        collection(db, "contributions"),
        where("email", "==", email),
        where("status", "!=", "settled"),
    );

    onSnapshot(q, (snapshot) => {
        console.log("Snapshot received, document count: ", snapshot.size);
        if (snapshot.empty) {
            container.innerHTML = `<div class="text-stone-500 text-center py-20 italic">You're all caught up! No active debts found for ${email}.</div>`;
            return;
        }
        let html = `<h1 class="text-xl font-black text-white mb-6 uppercase italic tracking-tighter">My Active Debts</h1>`;
        let total = 0;

        snapshot.forEach((snap) => {
            const data = snap.data();
            const amount = parseFloat(data.amount) || 0;
            total += amount;

            html += `
                <div class="bg-stone-900 border border-stone-800 p-5 rounded-3xl mb-4 shadow-2xl">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <span class="text-[10px] text-emerald-500 font-mono font-bold uppercase">Event: ${data.event_code}</span>
                            <h3 class="text-white font-bold text-lg">${data.label}</h3>
                        </div>
                        <div class="text-emerald-400 font-black text-xl">¥${amount.toLocaleString()}</div>
                    </div>
                    
                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-stone-800/50">
                        <span class="text-[10px] uppercase text-stone-500 font-bold">${data.status.replace("_", " ")}</span>
                        ${
                            data.status === "pending"
                                ? `
                            <button onclick="requestPaymentVerify('${snap.id}')" class="bg-emerald-600 text-white px-4 py-1.5 rounded-xl text-[11px] font-bold shadow-lg active:scale-95 transition">
                                Mark as Paid
                            </button>
                        `
                                : `<span class="text-[10px] text-amber-500 italic">Waiting for verification...</span>`
                        }
                    </div>
                </div>
            `;
        });
        html += `
            <div class="mt-10 p-6 bg-emerald-600 rounded-3xl flex justify-between items-center shadow-emerald-900/20 shadow-2xl">
                <span class="text-emerald-100 text-xs font-bold uppercase tracking-widest">Grand Total Owed</span>
                <span class="text-white text-3xl font-black italic">¥${total.toLocaleString()}</span>
            </div>
        `;
        container.innerHTML = html;
    });
}

window.requestPaymentVerify = async (id) => {
    const note = prompt("How did you settle payment?");
    if (!note) {
        return;
    }

    await updateDoc(doc(db, "contributions", id), {
        status: "pending_verification",
        payment_note: note,
        paid_at: serverTimestamp(),
    });
};
