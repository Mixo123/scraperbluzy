let allOffers = [];
let currentGender = 'all';

// Funkcje pomocnicze
const getPrice = (params) => {
    const priceParam = params.find(p => p.key === 'price');
    return priceParam?.value?.label || 'Cena do negocjacji';
};

const getImage = (photos) => {
    if (photos?.[0]?.link) {
        return photos[0].link.replace('{width}x{height}', '800x600');
    }
    return 'https://via.placeholder.com/300x200?text=Brak+zdjęcia';
};

// Pobieranie ofert
const fetchOffers = async () => {
    try {
        const response = await fetch('api.php');
        allOffers = await response.json();
        renderOffers();
    } catch (error) {
        console.error('Błąd pobierania ofert:', error);
        alert('Problem z pobraniem ofert');
    }
};

// Renderowanie ofert
const renderOffers = () => {
    const container = document.getElementById('offersContainer');
    const filtered = allOffers.filter(offer => 
        currentGender === 'all' || offer.gender === currentGender
    );

    container.innerHTML = filtered.map(offer => `
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <img src="${getImage(offer.photos)}" alt="${offer.title}" class="w-full h-48 object-cover">
            <div class="p-4">
                <h3 class="text-lg font-semibold mb-2">${offer.title || 'Brak tytułu'}</h3>
                <div class="text-green-600 font-bold mb-2">${getPrice(offer.params)}</div>
                <div class="flex justify-between items-center">
                    <a href="${offer.url}" target="_blank" class="text-blue-600 hover:text-blue-800">
                        Zobacz ofertę
                    </a>
                    <form class="add-to-favorites">
                        <input type="hidden" name="title" value="${offer.title}">
                        <input type="hidden" name="price" value="${getPrice(offer.params)}">
                        <input type="hidden" name="url" value="${offer.url}">
                        <input type="hidden" name="image" value="${getImage(offer.photos)}">
                        <input type="hidden" name="gender" value="${offer.gender}">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                            ♥ Dodaj do ulubionych
                        </button>
                    </form>
                </div>
            </div>
        </div>
    `).join('');
};

// Obsługa filtrów
document.getElementById('filters').addEventListener('click', (e) => {
    if (e.target.tagName === 'BUTTON') {
        currentGender = e.target.dataset.gender;
        
        // Aktualizacja przycisków
        document.querySelectorAll('#filters button').forEach(btn => {
            const isActive = btn.dataset.gender === currentGender;
            btn.classList.toggle('bg-blue-500', isActive);
            btn.classList.toggle('text-white', isActive);
            btn.classList.toggle('bg-white', !isActive);
        });

        // Aktualizacja nagłówka
        const titles = {
            all: '🔥 Wszystkie oferty',
            female: '👚 Bluzy Damskie',
            male: '👕 Bluzy Męskie'
        };
        document.getElementById('sectionTitle').textContent = titles[currentGender];
        
        renderOffers();
    }
});

// Obsługa dodawania do ulubionych
document.addEventListener('submit', async (e) => {
    if (e.target.classList.contains('add-to-favorites')) {
        e.preventDefault();
        
        try {
            const form = e.target;
            const formData = {
                title: form.querySelector('[name="title"]').value,
                price: form.querySelector('[name="price"]').value,
                url: form.querySelector('[name="url"]').value,
                image: form.querySelector('[name="image"]').value,
                gender: form.querySelector('[name="gender"]').value,
                add_to_favorites: true
            };

            const response = await fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json(); // Bezpośrednio parsuj odpowiedź jako JSON

            if (!response.ok) {
                throw new Error(result.message || 'Błąd serwera');
            }

            alert('Dodano do ulubionych!');
        } catch (error) {
            console.error('Błąd:', error);
            alert(error.message);
        }
    }
});

// Inicjalizacja
fetchOffers();