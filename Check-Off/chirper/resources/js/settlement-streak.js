import {
    collection,
    doc,
    getDoc,
    getDocs,
    query,
    serverTimestamp,
    setDoc,
    where,
} from "firebase/firestore";

export function normalizeSettlementUserKey(value) {
    return (value || "").trim().toLowerCase();
}

export async function getSettlementStreak(db, userEmail) {
    const userKey = normalizeSettlementUserKey(userEmail);

    if (!userKey) {
        return {
            user_key: "",
            current_streak: 0,
            best_streak: 0,
            last_result: null,
        };
    }

    const streakRef = doc(db, "settlement_streaks", userKey);
    const streakSnap = await getDoc(streakRef);

    if (!streakSnap.exists()) {
        return {
            user_key: userKey,
            current_streak: 0,
            best_streak: 0,
            last_result: null,
        };
    }

    return {
        user_key: userKey,
        ...streakSnap.data(),
    };
}

export async function incrementSettlementStreak(
    db,
    { userEmail, contributionId = null, eventCode = null }
) {
    const userKey = normalizeSettlementUserKey(userEmail);
    if (!userKey) return null;

    const streakRef = doc(db, "settlement_streaks", userKey);
    const streakSnap = await getDoc(streakRef);
    const existing = streakSnap.exists() ? streakSnap.data() : {};

    if (
        contributionId &&
        existing.last_processed_contribution_id === contributionId &&
        existing.last_result === "increment"
    ) {
        return {
            user_key: userKey,
            ...existing,
        };
    }

    const current = Number(existing.current_streak || 0);
    const best = Number(existing.best_streak || 0);
    const next = current + 1;

    const payload = {
        user_key: userKey,
        email: userKey,
        current_streak: next,
        best_streak: Math.max(best, next),
        last_result: "increment",
        last_processed_contribution_id: contributionId,
        last_event_code: eventCode,
        last_updated_at: serverTimestamp(),
    };

    await setDoc(streakRef, payload, { merge: true });

    return {
        ...existing,
        ...payload,
    };
}

export async function resetSettlementStreak(
    db,
    { userEmail, contributionId = null, eventCode = null, reason = "reset" }
) {
    const userKey = normalizeSettlementUserKey(userEmail);
    if (!userKey) return null;

    const streakRef = doc(db, "settlement_streaks", userKey);
    const streakSnap = await getDoc(streakRef);
    const existing = streakSnap.exists() ? streakSnap.data() : {};

    if (
        contributionId &&
        existing.last_processed_contribution_id === contributionId &&
        existing.last_result === reason
    ) {
        return {
            user_key: userKey,
            ...existing,
        };
    }

    const payload = {
        user_key: userKey,
        email: userKey,
        current_streak: 0,
        best_streak: Number(existing.best_streak || 0),
        last_result: reason,
        last_processed_contribution_id: contributionId,
        last_event_code: eventCode,
        last_updated_at: serverTimestamp(),
    };

    await setDoc(streakRef, payload, { merge: true });

    return {
        ...existing,
        ...payload,
    };
}

export async function resetStreakIfUserHasOverdueDebt(db, userEmail) {
    const userKey = normalizeSettlementUserKey(userEmail);
    if (!userKey) return false;

    const now = new Date();

    const q = query(
        collection(db, "contributions"),
        where("debtor_email", "==", userKey)
    );

    const snapshot = await getDocs(q);

    let hasOverdueDebt = false;
    let overdueContributionId = null;
    let overdueEventCode = null;

    snapshot.forEach((item) => {
        const data = item.data();
        const status = data.status || "pending";
        const dueAt = data.due_at?.toDate ? data.due_at.toDate() : null;

        if (
            dueAt &&
            dueAt < now &&
            ["pending", "pending_verification"].includes(status)
        ) {
            hasOverdueDebt = true;
            overdueContributionId = item.id;
            overdueEventCode = data.event_code || null;
        }
    });

    if (!hasOverdueDebt) {
        return false;
    }

    await resetSettlementStreak(db, {
        userEmail: userKey,
        contributionId: overdueContributionId,
        eventCode: overdueEventCode,
        reason: "overdue",
    });

    return true;
}