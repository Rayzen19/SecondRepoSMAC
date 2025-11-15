/**
 * Philippine Address Dropdown Handler
 * Populates Region, Province, Municipality, and Barangay dropdowns
 */

document.addEventListener('DOMContentLoaded', function() {
    const regionSelect = document.getElementById('region');
    const provinceSelect = document.getElementById('province');
    const municipalitySelect = document.getElementById('municipality');
    const barangaySelect = document.getElementById('barangay');

    if (!regionSelect) return; // Exit if not on a page with address fields

    // Get pre-selected values from data attributes
    const selectedRegion = regionSelect.getAttribute('data-selected-region');
    const selectedProvince = provinceSelect?.getAttribute('data-selected-province');
    const selectedMunicipality = municipalitySelect?.getAttribute('data-selected-municipality');
    const selectedBarangay = barangaySelect?.getAttribute('data-selected-barangay');

    // Philippine Regions Data
    const regions = [
        { code: 'NCR', name: 'National Capital Region (NCR)' },
        { code: 'CAR', name: 'Cordillera Administrative Region (CAR)' },
        { code: 'Region I', name: 'Region I - Ilocos Region' },
        { code: 'Region II', name: 'Region II - Cagayan Valley' },
        { code: 'Region III', name: 'Region III - Central Luzon' },
        { code: 'Region IV-A', name: 'Region IV-A - CALABARZON' },
        { code: 'Region IV-B', name: 'Region IV-B - MIMAROPA' },
        { code: 'Region V', name: 'Region V - Bicol Region' },
        { code: 'Region VI', name: 'Region VI - Western Visayas' },
        { code: 'Region VII', name: 'Region VII - Central Visayas' },
        { code: 'Region VIII', name: 'Region VIII - Eastern Visayas' },
        { code: 'Region IX', name: 'Region IX - Zamboanga Peninsula' },
        { code: 'Region X', name: 'Region X - Northern Mindanao' },
        { code: 'Region XI', name: 'Region XI - Davao Region' },
        { code: 'Region XII', name: 'Region XII - SOCCSKSARGEN' },
        { code: 'Region XIII', name: 'Region XIII - Caraga' },
        { code: 'BARMM', name: 'Bangsamoro Autonomous Region in Muslim Mindanao (BARMM)' }
    ];

    // Sample provinces by region (you can expand this)
    const provinces = {
        'NCR': ['Metro Manila'],
        'CAR': ['Abra', 'Apayao', 'Benguet', 'Ifugao', 'Kalinga', 'Mountain Province'],
        'Region I': ['Ilocos Norte', 'Ilocos Sur', 'La Union', 'Pangasinan'],
        'Region II': ['Batanes', 'Cagayan', 'Isabela', 'Nueva Vizcaya', 'Quirino'],
        'Region III': ['Aurora', 'Bataan', 'Bulacan', 'Nueva Ecija', 'Pampanga', 'Tarlac', 'Zambales'],
        'Region IV-A': ['Batangas', 'Cavite', 'Laguna', 'Quezon', 'Rizal'],
        'Region IV-B': ['Marinduque', 'Occidental Mindoro', 'Oriental Mindoro', 'Palawan', 'Romblon'],
        'Region V': ['Albay', 'Camarines Norte', 'Camarines Sur', 'Catanduanes', 'Masbate', 'Sorsogon'],
        'Region VI': ['Aklan', 'Antique', 'Capiz', 'Guimaras', 'Iloilo', 'Negros Occidental'],
        'Region VII': ['Bohol', 'Cebu', 'Negros Oriental', 'Siquijor'],
        'Region VIII': ['Biliran', 'Eastern Samar', 'Leyte', 'Northern Samar', 'Samar', 'Southern Leyte'],
        'Region IX': ['Zamboanga del Norte', 'Zamboanga del Sur', 'Zamboanga Sibugay'],
        'Region X': ['Bukidnon', 'Camiguin', 'Lanao del Norte', 'Misamis Occidental', 'Misamis Oriental'],
        'Region XI': ['Davao de Oro', 'Davao del Norte', 'Davao del Sur', 'Davao Occidental', 'Davao Oriental'],
        'Region XII': ['Cotabato', 'Sarangani', 'South Cotabato', 'Sultan Kudarat'],
        'Region XIII': ['Agusan del Norte', 'Agusan del Sur', 'Dinagat Islands', 'Surigao del Norte', 'Surigao del Sur'],
        'BARMM': ['Basilan', 'Lanao del Sur', 'Maguindanao', 'Sulu', 'Tawi-Tawi']
    };

    // Sample municipalities (you can expand this or use an API)
    const municipalities = {
        'Batangas': ['Agoncillo', 'Alitagtag', 'Balayan', 'Balete', 'Batangas City', 'Bauan', 'Calaca', 'Calatagan', 'Cuenca', 'Ibaan', 'Laurel', 'Lemery', 'Lian', 'Lipa City', 'Lobo', 'Mabini', 'Malvar', 'Mataas na Kahoy', 'Nasugbu', 'Padre Garcia', 'Rosario', 'San Jose', 'San Juan', 'San Luis', 'San Nicolas', 'San Pascual', 'Santa Teresita', 'Santo Tomas', 'Taal', 'Talisay', 'Tanauan City', 'Taysan', 'Tingloy', 'Tuy'],
        'Cavite': ['Alfonso', 'Amadeo', 'Bacoor', 'Carmona', 'Cavite City', 'Dasmariñas', 'General Mariano Alvarez', 'General Emilio Aguinaldo', 'General Trias', 'Imus', 'Indang', 'Kawit', 'Magallanes', 'Maragondon', 'Mendez', 'Naic', 'Noveleta', 'Rosario', 'Silang', 'Tagaytay', 'Tanza', 'Ternate', 'Trece Martires'],
        'Laguna': ['Alaminos', 'Bay', 'Biñan', 'Cabuyao', 'Calamba', 'Calauan', 'Cavinti', 'Famy', 'Kalayaan', 'Liliw', 'Los Baños', 'Luisiana', 'Lumban', 'Mabitac', 'Magdalena', 'Majayjay', 'Nagcarlan', 'Paete', 'Pagsanjan', 'Pakil', 'Pangil', 'Pila', 'Rizal', 'San Pablo City', 'San Pedro', 'Santa Cruz', 'Santa Maria', 'Santa Rosa', 'Siniloan', 'Victoria'],
        // Add more municipalities as needed
    };

    // Sample barangays (you can expand this or use an API)
    const barangays = {
        'Lipa City': ['Adya', 'Anilao', 'Anilao-Labac', 'Antipolo del Norte', 'Antipolo del Sur', 'Bagong Pook', 'Balintawak', 'Banaybanay', 'Bolbok', 'Bugtong na Pulo', 'Bulacnin', 'Bulaklakan', 'Calamias', 'Cumba', 'Dagatan', 'Duhatan', 'Halang', 'Inosloban', 'Kayumanggi', 'Latag', 'Lodlod', 'Lumbang', 'Mabini', 'Malagonlong', 'Malitlit', 'Marauoy', 'Mataas na Lupa', 'Munting Pulo', 'Pagolingin Bata', 'Pagolingin East', 'Pagolingin West', 'Pangao', 'Pinagkawitan', 'Pinagtongulan', 'Plaridel', 'Poblacion Barangay 1', 'Poblacion Barangay 2', 'Poblacion Barangay 3', 'Poblacion Barangay 4', 'Poblacion Barangay 5', 'Poblacion Barangay 6', 'Poblacion Barangay 7', 'Poblacion Barangay 8', 'Poblacion Barangay 9', 'Poblacion Barangay 9-A', 'Poblacion Barangay 10', 'Poblacion Barangay 11', 'Poblacion Barangay 12', 'Pusil', 'Quezon', 'Rizal', 'Sabang', 'Sampaguita', 'San Benito', 'San Carlos', 'San Celestino', 'San Francisco', 'San Guillermo', 'San Jose', 'San Lucas', 'San Salvador', 'San Sebastian', 'Santo Niño', 'Santo Toribio', 'Sapac', 'Sico', 'Talisay', 'Tambo', 'Tangob', 'Tanguay', 'Tibig', 'Tipacan'],
        // Add more barangays as needed
    };

    // Populate regions on load
    function populateRegions() {
        regionSelect.innerHTML = '<option value="">Select Region</option>';
        regions.forEach(region => {
            const option = document.createElement('option');
            option.value = region.name;
            option.textContent = region.name;
            if (selectedRegion && region.name === selectedRegion) {
                option.selected = true;
            }
            regionSelect.appendChild(option);
        });

        // If there's a pre-selected region, load its provinces
        if (selectedRegion) {
            populateProvinces(selectedRegion);
        }
    }

    // Populate provinces based on selected region
    function populateProvinces(regionName) {
        if (!provinceSelect) return;

        provinceSelect.innerHTML = '<option value="">Select Province</option>';
        municipalitySelect.innerHTML = '<option value="">Select Municipality</option>';
        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';

        const regionCode = regions.find(r => r.name === regionName)?.code;
        const provinceList = provinces[regionCode] || [];

        provinceList.forEach(province => {
            const option = document.createElement('option');
            option.value = province;
            option.textContent = province;
            if (selectedProvince && province === selectedProvince) {
                option.selected = true;
            }
            provinceSelect.appendChild(option);
        });

        // If there's a pre-selected province, load its municipalities
        if (selectedProvince) {
            populateMunicipalities(selectedProvince);
        }
    }

    // Populate municipalities based on selected province
    function populateMunicipalities(province) {
        if (!municipalitySelect) return;

        municipalitySelect.innerHTML = '<option value="">Select Municipality</option>';
        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';

        const municipalityList = municipalities[province] || [];

        municipalityList.forEach(municipality => {
            const option = document.createElement('option');
            option.value = municipality;
            option.textContent = municipality;
            if (selectedMunicipality && municipality === selectedMunicipality) {
                option.selected = true;
            }
            municipalitySelect.appendChild(option);
        });

        // If there's a pre-selected municipality, load its barangays
        if (selectedMunicipality) {
            populateBarangays(selectedMunicipality);
        }
    }

    // Populate barangays based on selected municipality
    function populateBarangays(municipality) {
        if (!barangaySelect) return;

        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';

        const barangayList = barangays[municipality] || [];

        barangayList.forEach(barangay => {
            const option = document.createElement('option');
            option.value = barangay;
            option.textContent = barangay;
            if (selectedBarangay && barangay === selectedBarangay) {
                option.selected = true;
            }
            barangaySelect.appendChild(option);
        });
    }

    // Event listeners for cascading dropdowns
    regionSelect.addEventListener('change', function() {
        populateProvinces(this.value);
    });

    if (provinceSelect) {
        provinceSelect.addEventListener('change', function() {
            populateMunicipalities(this.value);
        });
    }

    if (municipalitySelect) {
        municipalitySelect.addEventListener('change', function() {
            populateBarangays(this.value);
        });
    }

    // Initialize on page load
    populateRegions();
});
