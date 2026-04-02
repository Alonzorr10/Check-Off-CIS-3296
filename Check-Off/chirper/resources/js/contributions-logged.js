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
import { getAuth, onAuthStateChanged } from "firebase/auth";
import {
    getSettlementStreak,
    normalizeSettlementUserKey,
    resetStreakIfUserHasOverdueDebt,
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

let unsubscribe = null;

function formatDate(timestamp) {
    if (!timestamp?.toDate) return "No due date";
    return timestamp.toDate().toLocaleString();
}

function isOverdue(data) {
    const dueAt = data.due_at?.toDate ? data.due_at.toDate() : null;
    if (!dueAt) return false;

    const status = data.status || "pending";
    return (
        dueAt < new Date() &&
        ["pending", "pending_verification"].includes(status)
    );
}

async function renderStreak(email) {
    const streak = await getSettlementStreak(db, email);

    document.getElementById("streak-current").innerText = String(
        streak.current_streak || 0,
    );
    document.getElementById("streak-best").innerText = String(
        streak.best_streak || 0,
    );
    document.getElementById("streak-last-result").innerText =
        streak.last_result || "none";
}

function renderContributions(snapshot) {
    const container = document.getElementById("contributions-container");

    if (snapshot.empty) {
        container.innerHTML = `
            <div class="rounded-2xl border border-stone-700 bg-stone-900 p-6 text-center text-stone-400">
                You currently have no contributions assigned to your account.
            </div>
        `;
        return;
    }

    let html = "";
    let total = 0;

    snapshot.forEach((itemDoc) => {
        const data = itemDoc.data();
        const amount = parseFloat(data.amount) || 0;
        total += amount;

        const status = data.status || "pending";
        const overdue = isOverdue(data);

        html += `
            <div class="bg-stone-900 p-5 rounded-2xl mb-4 border border-stone-800 shadow-lg">
                <div class="flex justify-between items-start gap-4">
                    <div>
                        <div class="text-white font-bold text-lg">${data.label}</div>
                        <div class="text-stone-400 text-sm mt-1">Event code: ${data.event_code || "-"}</div>
                        <div class="text-stone-500 text-sm">Due: ${formatDate(data.due_at)}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-emerald-400 font-mono text-xl">¥${amount.toLocaleString()}</div>
                        <div class="mt-2 text-[10px] uppercase tracking-widest ${
                            status === "settled"
                                ? "text-emerald-400"
                                : status === "pending_verification"
                                  ? "text-amber-300"
                                  : status === "denied"
                                    ? "text-red-400"
                                    : overdue
                                      ? "text-red-500"
                                      : "text-stone-500"
                        }">
                            ${overdue ? "overdue" : status.replace("_", " ")}
                        </div>
                    </div>
                </div>

                ${
                    data.payment_note
                        ? `
                    <div class="mt-3 p-3 bg-stone-950 rounded-lg border border-stone-800 text-[12px] text-stone-400">
                        Payment note: ${data.payment_note}
                    </div>
                `
                        : ""
                }

                ${
                    ["pending", "denied"].includes(status)
                        ? `
                    <button onclick="markAsPaid('${itemDoc.id}')"
                            class="mt-4 w-full py-2 bg-emerald-700 hover:bg-emerald-600 text-white text-sm font-bold rounded-lg transition">
                        Submit Payment
                    </button>
                `
                        : ""
                }
            </div>
        `;
    });

    html += `
        <div class="mt-6 pt-4 border-t border-stone-700 flex justify-between items-center">
            <div class="text-stone-400 text-sm uppercase tracking-tighter">Total Outstanding</div>
            <div class="text-white text-2xl font-bold">¥${total.toLocaleString()}</div>
        </div>
    `;

    container.innerHTML = html;
}

function listenToContributions(email) {
    if (unsubscribe) unsubscribe();

    const q = query(
        collection(db, "contributions"),
        where("debtor_email", "==", normalizeSettlementUserKey(email)),
    );

    unsubscribe = onSnapshot(q, async (snapshot) => {
        renderContributions(snapshot);
        await renderStreak(email);
    });
}

window.markAsPaid = async function (docId) {
    const note = prompt("Describe how you paid this debt.");
    if (note === null) return;

    if (!note.trim()) {
        alert("Payment note is required.");
        return;
    }

    try {
        const docRef = doc(db, "contributions", docId);
        await updateDoc(docRef, {
            status: "pending_verification",
            payment_note: note.trim(),
            paid_at: serverTimestamp(),
        });
    } catch (error) {
        console.error("Error updating contribution:", error);
        alert("Something went wrong while submitting payment.");
    }
};

onAuthStateChanged(auth, async (user) => {
    if (!user || !user.email) {
        window.location.href = "/login";
        return;
    }

    await resetStreakIfUserHasOverdueDebt(db, user.email);
    await renderStreak(user.email);
    listenToContributions(user.email);
});
