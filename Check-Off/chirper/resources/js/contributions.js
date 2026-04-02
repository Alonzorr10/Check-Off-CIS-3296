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

// 1. INPUT BEHAVIOR (Auto-focus next box)
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

// 2. SEARCH LOGIC
searchBtn.addEventListener("click", () => {
    const code = Array.from(inputs)
        .map((i) => i.value)
        .join("")
        .toUpperCase();
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

    // UPDATED QUERY:
    // We search by event_code and name.
    // We don't filter by 'email' here because guests don't have linked emails.
    const q = query(
        collection(db, "contributions"),
        where("event_code", "==", code),
        where("debtor_name", "==", name),
    );

    unsubscribe = onSnapshot(
        q,
        (snapshot) => {
            renderContributions(snapshot);
        },
        (error) => {
            console.error("Search error:", error);
        },
    );
}

// 3. UI RENDERING
function renderContributions(snapshot) {
    const container = document.getElementById("contributions-container");

    if (snapshot.empty) {
        container.innerHTML = `
            <div class="text-stone-500 text-sm mt-8 text-center italic border-2 border-dashed border-stone-800 p-6 rounded-2xl">
                No items found for this name in Event ${Array.from(inputs)
                    .map((i) => i.value)
                    .join("")}.
                <br><span class="text-[10px]">Try checking for typos or ask the owner if you are a "Guest" or "User".</span>
            </div>`;
        return;
    }

    let html = `<h2 class="text-xs font-bold text-stone-500 uppercase tracking-widest mb-4">Results found:</h2>`;
    let total = 0;

    snapshot.forEach((docSnap) => {
        const data = docSnap.data();
        const amount = parseFloat(data.amount) || 0;
        total += amount;

        const status = data.status || "pending";
        const isPending = status === "pending";
        const isVerifying = status === "pending_verification";
        const isSettled = status === "settled";

        html += `
            <div class="bg-stone-900 p-5 rounded-2xl mb-3 border border-stone-800 shadow-xl">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="text-white font-bold text-lg">${data.label}</div>
                        <div class=" text-white text-[10px] uppercase tracking-widest mt-1 font-bold
                            ${isSettled ? "text-emerald-500" : isVerifying ? "text-amber-400 animate-pulse" : "text-stone-500"}">
                            ${status.replace("_", " ")}
                        </div>
                    </div>
                    <div class="text-emerald-500 text-xl">
                        ¥${amount.toLocaleString()}
                    </div>
                </div>

                ${
                    isPending
                        ? `
                    <button onclick="markAsPaid('${docSnap.id}')"
                            class="mt-4 w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-bold rounded-xl transition-all active:scale-95 shadow-lg shadow-emerald-900/20">
                        Mark as Paid
                    </button>
                `
                        : ""
                }

                ${
                    data.payment_note
                        ? `
                    <div class="mt-3 p-3 bg-stone-950/50 rounded-lg border border-stone-800 text-[10px] text-stone-400 italic">
                        Note: ${data.payment_note}
                    </div>
                `
                        : ""
                }
            </div>
        `;
    });

    html += `
        <div class="text-white mt-8 p-5 bg-stone-900 border border-emerald-900/30 rounded-2xl flex justify-between items-center shadow-2xl">
            <div class="text-stone-400 text-xs font-bold uppercase tracking-widest">Total Debt</div>
            <div class="text-white text-3xl italic underline decoration-emerald-500">¥${total.toLocaleString()}</div>
        </div>
    `;
    container.innerHTML = html;
}

// 4. ACTION FUNCTIONS
window.markAsPaid = async function (docId) {
    const note = prompt("How did you pay? (e.g., PayPay, LinePay, Cash)");
    if (!note) return;

    try {
        const docRef = doc(db, "contributions", docId);
        await updateDoc(docRef, {
            status: "pending_verification",
            payment_note: note,
            paid_at: serverTimestamp(),
        });
        alert("Payment sent for verification!");
    } catch (error) {
        console.error("Update error:", error);
        alert("Could not update. Check your connection.");
    }
};
