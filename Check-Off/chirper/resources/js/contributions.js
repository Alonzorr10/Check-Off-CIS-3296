import { initializeApp } from "firebase/app";
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

const inputs = document.querySelectorAll('input[maxlength="1"]');
const nameInput = document.getElementById("name");
const searchBtn = document.getElementById("view-contributions");

inputs.forEach((input, index) => {
    input.addEventListener("input", (e) => {
        if (e.target.value.length === 1 && index < inputs.length - 1) {
            inputs[index + 1].focus();
        }
    });
    input.addEventListener("keydown", (e) => {
        if (e.key === "Backspace" && !e.target.value && index > 0) {
            inputs[index - 1].focus();
        }
    });
});

searchBtn.addEventListener("click", () => {
    const code = Array.from(inputs)
        .map((i) => i.value)
        .join("");
    const name = nameInput.value.trim();

    if (code.length === 6 && name.length > 0) {
        searchBtn.innerText = "Searching...";
        searchBtn.disabled = true;
        fetchContributions(code, name);
        setTimeout(() => {
            searchBtn.innerText = "View Contributions Owed";
            searchBtn.disabled = false;
        }, 1000);
    } else {
        alert("Please enter the full 6-digit code and your name.");
    }
});

let unsubscribe = null;

function fetchContributions(code, name) {
    if (unsubscribe) unsubscribe();
    const q = query(
        collection(db, "contributions"),
        where("event_code", "==", code),
        where("debtor_name", "==", name),
    );
    unsubscribe = onSnapshot(q, (snapshot) => {
        renderContributions(snapshot);
    });
}

function renderContributions(snapshot) {
    const container = document.getElementById("contributions-container");
    if (snapshot.empty) {
        container.innerHTML = `<div class="text-stone-500 text-sm mt-4 text-center italic">No contributions found. Check for typos!</div>`;
        return;
    }

    let html = "";
    let total = 0;

    snapshot.forEach((doc) => {
        const data = doc.data();
        const amount = parseFloat(data.amount) || 0;
        total += amount;

        const isPending = data.status === "pending";
        const isVerifying = data.status === "pending_verification";
        const isSettled = data.status === "settled";

        html += `
            <div class="bg-stone-800 p-4 rounded-xl mb-3 border border-stone-700 shadow-lg">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="text-white font-bold">${data.label}</div>
                        <div class="text-[10px] uppercase tracking-widest mt-1 
                            ${
                                isSettled
                                    ? "text-emerald-400"
                                    : isVerifying
                                      ? "text-amber-300 animate-pulse"
                                      : "text-amber-600"
                            }">
                            ${data.status.replace("_", " ")}
                        </div>
                    </div>
                    <div class="text-emerald-400 font-mono text-lg">
                        ¥${amount.toLocaleString()}
                    </div>
                </div>

                ${
                    isPending
                        ? `
                    <button onclick="markAsPaid('${doc.id}')" 
                            class="mt-4 w-full py-2 bg-stone-700 hover:bg-emerald-700 text-white text-[11px] font-bold rounded-lg transition-all active:scale-95">
                        Mark as Paid
                    </button>
                `
                        : ""
                }

                ${
                    data.payment_note
                        ? `
                    <div class="mt-3 p-2 bg-stone-900/50 rounded border border-stone-700/50 text-[10px] text-stone-400 italic">
                        Note: ${data.payment_note}
                    </div>
                `
                        : ""
                }
            </div>
        `;
    });

    html += `
        <div class="mt-6 pt-4 border-t border-stone-700 flex justify-between items-center">
            <div class="text-stone-400 text-sm uppercase tracking-tighter">Your Total Debt</div>
            <div class="text-white text-2xl font-bold italic">¥${total.toLocaleString()}</div>
        </div>
    `;
    container.innerHTML = html;
}

window.markAsPaid = async function (docId) {
    const note = prompt("How did you pay? (e.g., 'Sent 500 yen via PayPay')");
    if (note === null) return;

    try {
        const docRef = doc(db, "contributions", docId);
        await updateDoc(docRef, {
            status: "pending_verification",
            payment_note: note,
            paid_at: serverTimestamp(),
        });
    } catch (error) {
        console.error("Error updating status:", error);
        alert("Something went wrong. Please try again.");
    }
};
