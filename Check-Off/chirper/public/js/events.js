let events = JSON.parse(localStorage.getItem("checkoff_data")) || [];

window.renderUI = function () {
    const container = document.getElementById("event-container");
    if (!container) return;
    container.innerHTML = "";

    events.forEach((event) => {
        const total = event.items.reduce(
            (sum, item) => sum + parseFloat(item.amount),
            0,
        );

        const eventHtml = `
            <div class="bg-white border border-black/15 rounded-xl p-5 mb-3 shadow-sm" id="block-${event.id}">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-[15px] font-semibold text-[#1a1a1a]">${event.name}</h3>
                        <span class="text-[11px] text-black-400 font-mono">CODE: ${event.code}</span>
                    </div>
                    <button onclick="deleteBlock('${event.id}')" class="text-black-300 hover:text-red-500 text-[11px]">Delete</button>
                </div>

                <div class="space-y-2 mb-4">
                    ${event.items
                        .map(
                            (item) => `
                        <div class="flex justify-between items-center py-1 border-b border-black/5">
                            <div class="text-[13px]">${item.label} <span class="text-[11px] text-gray-400">(${item.owner})</span></div>
                            <div class="text-[13px] font-medium text-[#1D9E75]">$${item.amount}</div>
                        </div>
                    `,
                        )
                        .join("")}
                    ${event.items.length > 0 ? `<div class="text-right font-bold text-[13px] mt-2">Total: $${total}</div>` : ""}
                </div>

                <div id="form-${event.id}" class="hidden bg-gray-50 p-3 rounded-lg mb-3 border border-dashed border-gray-300">
                    <input type="text" id="label-${event.id}" placeholder="Item (e.g. Gas)" class="text-[12px] w-full p-2 mb-2 border rounded">
                    <div class="flex gap-2 mb-2">
                        <input type="text" id="owner-${event.id}" placeholder="Who owes?" class="text-[12px] w-full p-2 border rounded">
                        <input type="number" id="amount-${event.id}" placeholder="$" class="text-[12px] w-20 p-2 border rounded">
                    </div>
                    <div class="flex gap-2">
                        <button onclick="saveSubCategory('${event.id}')" class="bg-[#1D9E75] text-black px-3 py-1 rounded text-[11px]">Add Item</button>
                        <button onclick="toggleForm('${event.id}')" class="text-black-500 text-[11px]">Cancel</button>
                    </div>
                </div>

                <button onclick="toggleForm('${event.id}')" class="w-full py-2 border border-dashed border-gray-300 rounded-lg text-[12px] text-black-400 hover:bg-black-50">
                    + Add Sub-Category
                </button>
            </div>
        `;
        container.insertAdjacentHTML("beforeend", eventHtml);
    });
};
window.addNewEventBlock = function () {
    const container = document.getElementById("event-container");
    const ID = Date.now();
    const randomCode = Math.random().toString(36).substring(2, 8).toUpperCase();

    const eventHtml = `
        <div class="bg-white border-2 border-[#1D9E75] rounded-xl p-5 mb-3 shadow-md" id="temp-${ID}">
            <div class="flex flex-col gap-3">
                <input type="text" id="input-name-${ID}" class="text-[14px] p-2 border border-black/10 rounded-lg outline-none" placeholder="Event Name">
                <div class="flex gap-2">
                    <button onclick="confirmNewEvent('${ID}', '${randomCode}')" class="bg-[#1D9E75] text-black px-4 py-2 rounded-lg text-[13px]">Save Event</button>
                    <button onclick="document.getElementById('temp-${ID}').remove()" class="text-red-500 text-[13px]">Cancel</button>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML("afterbegin", eventHtml);
};

window.confirmNewEvent = function (tempID, code) {
    const name = document.getElementById(`input-name-${tempID}`).value.trim();
    if (!name) return alert("Enter a name");

    events.push({ id: tempID.toString(), name: name, code: code, items: [] });
    saveAndRefresh();
};

window.saveSubCategory = function (eventID) {
    const label = document.getElementById(`label-${eventID}`).value.trim();
    const owner = document.getElementById(`owner-${eventID}`).value.trim();
    const amount = document.getElementById(`amount-${eventID}`).value.trim();

    if (!label || !owner || !amount) return alert("Fill everything out");

    const event = events.find((e) => e.id === eventID);
    event.items.push({ label, owner, amount });
    saveAndRefresh();
};

window.toggleForm = (id) =>
    document.getElementById(`form-${id}`).classList.toggle("hidden");

window.deleteBlock = (id) => {
    events = events.filter((e) => e.id !== id);
    saveAndRefresh();
};

function saveAndRefresh() {
    localStorage.setItem("checkoff_data", JSON.stringify(events));
    window.renderUI();
}

document.addEventListener("DOMContentLoaded", window.renderUI);
