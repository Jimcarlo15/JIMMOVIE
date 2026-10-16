let currentCategory = 'ALL';

async function fetchCatalog() {
    try {
        const response = await fetch(`api.php?category=${currentCategory}`);
        const data = await response.json();
        
        renderCatalog(data);
        
        document.getElementById('loader').classList.add('hidden');
        document.getElementById('mediaGrid').classList.remove('hidden');
        lucide.createIcons();

    } catch (err) {
        console.error("Failed to fetch catalog:", err);
    }
}

function renderCatalog(items) {
    const grid = document.getElementById('mediaGrid');
    const searchVal = document.getElementById('searchInput').value.toLowerCase();

    let html = '';
    let count = 0;

    items.forEach(row => {
        if (searchVal && !row.title.toLowerCase().includes(searchVal) && !row.description.toLowerCase().includes(searchVal)) {
            return;
        }

        count++;
        html += `
            <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden hover:border-slate-700 transition flex flex-col group">
                <div class="relative h-72 overflow-hidden bg-slate-950">
                    <img src="${row.poster_url}" alt="${row.title}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    <div class="absolute top-3 left-3">
                        <span class="px-2 py-1 rounded text-xs font-bold uppercase tracking-wider ${row.category === 'Anime' ? 'bg-purple-600 text-white' : 'bg-blue-600 text-white'}">
                            ${row.category}
                        </span>
                    </div>
                    <div class="absolute top-3 right-3 bg-slate-950/80 backdrop-blur px-2 py-1 rounded-lg border border-slate-800 text-amber-400 font-bold text-xs flex items-center gap-1">
                        <i data-lucide="star" class="w-3 h-3 fill-amber-400"></i> ${row.rating}
                    </div>
                </div>

                <div class="p-4 flex flex-col flex-grow">
                    <div class="flex justify-between items-start mb-1">
                        <h3 class="font-bold text-white text-base leading-tight">${row.title}</h3>
                        <span class="text-xs text-slate-500 font-mono">${row.year}</span>
                    </div>

                    <p class="text-xs text-amber-500/80 mb-3 font-medium">${row.genre}</p>
                    <p class="text-slate-400 text-xs line-clamp-3 mb-4 flex-grow leading-relaxed">${row.description}</p>

                    <div class="pt-3 border-t border-slate-800/80 flex justify-between items-center text-[10px] text-slate-500 uppercase tracking-wider">
                        <span>Information Only</span>
                        <span class="text-slate-600">ID: #${row.id}</span>
                    </div>
                </div>
            </div>
        `;
    });

    if (count === 0) {
        grid.innerHTML = `
            <div class="col-span-full text-center py-12 text-slate-500">
                <i data-lucide="film" class="w-12 h-12 mx-auto mb-2 opacity-50"></i>
                <p>No titles found matching your search query.</p>
            </div>
        `;
    } else {
        grid.innerHTML = html;
    }

    lucide.createIcons();
}

function setCategoryFilter(category) {
    currentCategory = category;
    
    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active-tab'));
    document.getElementById(`filter-${category}`).classList.add('active-tab');

    fetchCatalog();
}

function filterMedia() {
    fetchCatalog();
}

async function handleAddSubmit(e) {
    e.preventDefault();

    const formData = {
        title: document.getElementById('title').value,
        category: document.getElementById('category').value,
        rating: parseFloat(document.getElementById('rating').value),
        genre: document.getElementById('genre').value,
        year: parseInt(document.getElementById('year').value),
        posterUrl: document.getElementById('posterUrl').value,
        description: document.getElementById('description').value
    };

    const response = await fetch('api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
    });

    if (response.ok) {
        document.getElementById('addForm').reset();
        toggleModal('addModal');
        fetchCatalog();
    }
}

function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.toggle('hidden');
}

window.addEventListener('DOMContentLoaded', fetchCatalog);