@extends('admin.components.template')

@section('breadcrumb')
<!-- Breadcrumb -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Teacher</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a>
                </li>
                <li class="breadcrumb-item">Teacher</li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.teachers.index') }}">Teacher Lists</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Teacher Edit</li>
            </ol>
        </nav>
    </div>
</div>
<!-- /Breadcrumb -->
@endsection

@section('content')
<div class="content">
    <div class="card">
        <div class="card-body p-5">
            <form action="{{ route('admin.teachers.update', $teacher) }}" method="POST" class="max-w-2xl mx-auto bg-white p-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Employee # <span class="text-danger">*</span></label>
                            <input type="text" name="employee_number" class="form-control bg-light" value="{{ old('employee_number', $teacher->employee_number) }}" readonly>
                            <small class="form-text text-muted">Auto-generated (Cannot be changed)</small>
                            @error('employee_number')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $teacher->first_name) }}" required>
                            @error('first_name')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Middle Name</label>
                            <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $teacher->middle_name) }}">
                            @error('middle_name')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $teacher->last_name) }}" required>
                            @error('last_name')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Suffix</label>
                            <input type="text" name="suffix" class="form-control" value="{{ old('suffix', $teacher->suffix) }}">
                            @error('suffix')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select" required>
                                <option value="male" @selected(old('gender', $teacher->gender)==='male')>Male</option>
                                <option value="female" @selected(old('gender', $teacher->gender)==='female')>Female</option>
                            </select>
                            @error('gender')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $teacher->email) }}" required>
                            @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="number" name="phone" class="form-control" value="{{ old('phone', $teacher->phone) }}" min="0" max="99999999999" placeholder="09XXXXXXXXX" oninput="if(this.value.length > 11) this.value = this.value.slice(0, 11);">
                            @error('phone')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">House no./ Street/ Subdivision</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address', $teacher->address) }}" placeholder="Enter house number, street, or subdivision">
                            @error('address')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Region</label>
                            <select name="region" id="region" class="form-select" data-ph-address="true" data-selected-region="{{ old('region', $teacher->region) }}">
                                <option value="">Select Region</option>
                            </select>
                            @error('region')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Province</label>
                            <select name="province" id="province" class="form-select" data-selected-province="{{ old('province', $teacher->province) }}">
                                <option value="">Select Province</option>
                            </select>
                            @error('province')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Municipality</label>
                            <select name="municipality" id="municipality" class="form-select" data-selected-municipality="{{ old('municipality', $teacher->municipality) }}">
                                <option value="">Select Municipality</option>
                            </select>
                            @error('municipality')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Barangay</label>
                            <select name="barangay" id="barangay" class="form-select" data-selected-barangay="{{ old('barangay', $teacher->barangay) }}">
                                <option value="">Select Barangay</option>
                            </select>
                            @error('barangay')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Postal Code <span class="text-danger">*</span></label>
                            <input type="number" name="postal_code" class="form-control" value="{{ old('postal_code', $teacher->postal_code) }}" min="0" max="9999" placeholder="0000" title="Please enter exactly 4 digits" oninput="if(this.value.length > 4) this.value = this.value.slice(0, 4);" required>
                            @error('postal_code')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Department <span class="text-danger">*</span></label>
                            <input type="text" name="department" class="form-control" value="{{ old('department', $teacher->department) }}" required>
                            @error('department')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Specialization</label>
                            <input type="text" name="specialization" class="form-control" value="{{ old('specialization', $teacher->specialization) }}">
                            @error('specialization')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Term <span class="text-danger">*</span></label>
                            <input type="text" name="term" class="form-control" value="{{ old('term', $teacher->term) }}" required>
                            @error('term')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="active" @selected(old('status', $teacher->status)==='active')>Active</option>
                                <option value="inactive" @selected(old('status', $teacher->status)==='inactive')>Inactive</option>
                                <option value="retired" @selected(old('status', $teacher->status)==='retired')>Retired</option>
                                <option value="resigned" @selected(old('status', $teacher->status)==='resigned')>Resigned</option>
                            </select>
                            @error('status')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Subjects to Teach</label>
                            <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto; background-color: #f8f9fa;">
                                <div class="row">
                                    @foreach($subjects as $subject)
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="subjects[]" value="{{ $subject->id }}" id="subject_{{ $subject->id }}" @checked(in_array($subject->id, old('subjects', $teacher->subjects->pluck('id')->toArray())))>
                                            <label class="form-check-label" for="subject_{{ $subject->id }}">
                                                {{ $subject->name }} @if($subject->code)<span class="text-muted">({{ $subject->code }})</span>@endif
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <small class="form-text text-muted">Select all subjects this teacher will teach</small>
                            @error('subjects')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <a href="{{ url()->previous() }}" class="btn btn-outline-light border me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
@endsection

<script>
// CALABARZON Address Data
const addressData = {
    region: "Region IV-A (CALABARZON)",
    provinces: {
        "Cavite": {
            municipalities: ["Alfonso", "Amadeo", "Bacoor City", "Carmona", "Cavite City", "Dasmariñas City", "General Emilio Aguinaldo", "General Mariano Alvarez", "General Trias City", "Imus City", "Indang", "Kawit", "Magallanes", "Maragondon", "Mendez", "Naic", "Noveleta", "Rosario", "Silang", "Tagaytay City", "Tanza", "Ternate", "Trece Martires City"],
            barangays: {
                "Alfonso": ["Poblacion I", "Poblacion II", "Barangay I", "Kaysuyo", "Luksuhin", "Mangas I", "Mangas II"],
                "Amadeo": ["Poblacion", "Bucal", "Dagatan", "Halang", "Loma", "Maitim I", "Maitim II"],
                "Bacoor City": ["Alima", "Aniban I", "Aniban II", "Aniban III", "Aniban IV", "Aniban V", "Banalo", "Bayanan", "Campo Santo", "Daang Bukid", "Digman", "Dulong Bayan", "Habay I", "Habay II", "Ligas I", "Ligas II", "Ligas III", "Mabolo I", "Mabolo II", "Mabolo III", "Molino I", "Molino II", "Molino III", "Molino IV", "Molino V", "Molino VI", "Molino VII", "Niog I", "Niog II", "Niog III", "Panapaan I", "Panapaan II", "Panapaan III", "Panapaan IV", "Panapaan V", "Panapaan VI", "Panapaan VII", "Panapaan VIII", "Queens Row Central", "Queens Row East", "Queens Row West", "Real I", "Real II", "Salinas I", "Salinas II", "Salinas III", "Salinas IV", "San Nicolas I", "San Nicolas II", "Sineguelasan", "Tabing Dagat", "Talaba I", "Talaba II", "Talaba III", "Talaba IV", "Talaba V", "Talaba VI", "Talaba VII"],
                "Carmona": ["Poblacion", "Bancal", "Cabilang Baybay", "Lantic", "Mabuhay", "Maduya", "Milagrosa"],
                "Cavite City": ["Barangay 1", "Barangay 2", "Barangay 3", "Barangay 4", "Barangay 5", "Barangay 6", "Barangay 7", "Barangay 8", "Barangay 9", "Barangay 10", "Barangay 11", "Barangay 12", "Barangay 13", "Barangay 14", "Barangay 15", "Barangay 16", "Barangay 17", "Barangay 18", "Barangay 19", "Barangay 20", "Barangay 21", "Barangay 22", "Barangay 23", "Barangay 24", "Barangay 25", "Barangay 26", "Barangay 27", "Barangay 28", "Barangay 29", "Barangay 30", "Barangay 31", "Barangay 32", "Barangay 33", "Barangay 34", "Barangay 35", "Barangay 36", "Barangay 37", "Barangay 38", "Barangay 39", "Barangay 40", "Barangay 41", "Barangay 42", "Barangay 43", "Barangay 44", "Barangay 45", "Barangay 46", "Barangay 47", "Barangay 48", "Barangay 49", "Barangay 50", "Barangay 51", "Barangay 52", "Barangay 53", "Barangay 54", "Barangay 55", "Barangay 56", "Barangay 57", "Barangay 58", "Barangay 59", "Barangay 60", "Barangay 61", "Barangay 62", "Barangay 63", "Barangay 64", "Barangay 65", "Barangay 66", "Barangay 67", "Barangay 68", "Barangay 69", "Barangay 70", "Barangay 71", "Barangay 72", "Barangay 73", "Barangay 74", "Barangay 75", "Barangay 76", "Barangay 77", "Barangay 78", "Barangay 79", "Barangay 80", "Barangay 81", "Barangay 82", "Barangay 83", "Barangay 84"],
                "Dasmariñas City": ["Burol I", "Burol II", "Burol III", "Datu Esmael", "Emmanuel Bergado I", "Emmanuel Bergado II", "Fatima I", "Fatima II", "Fatima III", "Langkaan I", "Langkaan II", "Paliparan I", "Paliparan II", "Paliparan III", "Salawag", "Sampaloc I", "Sampaloc II", "Sampaloc III", "Sampaloc IV", "Sampaloc V", "San Agustin I", "San Agustin II", "San Agustin III", "San Jose", "San Luis I", "San Luis II", "San Simon", "Santa Cristina I", "Santa Cristina II", "Zone I", "Zone II", "Zone III", "Zone IV"],
                "General Emilio Aguinaldo": ["Poblacion I", "Poblacion II", "Batas Dao", "Castaños Cerca", "Kaybagal Central", "Kabulusan"],
                "General Mariano Alvarez": ["Poblacion", "Barangay I", "Barangay II", "Barangay III", "Benjamin Tirona", "Epifanio Malia"],
                "General Trias City": ["Alingaro", "Artemio G. Reyes", "Bacao I", "Bacao II", "Bagumbayan", "Buenavista I", "Buenavista II", "Corregidor", "Dulong Bayan", "Governor Ferrer", "Javalera", "Manggahan", "Navarro", "Pasong Camachile I", "Pasong Camachile II", "Pinagtipunan", "San Francisco", "San Juan I", "San Juan II", "Tejero"],
                "Imus City": ["Alapan I", "Alapan II", "Anabu I", "Anabu II", "Anabu III", "Anabu IV", "Anabu V", "Anabu VI", "Anabu VII", "Anabu VIII", "Bayan Luma I", "Bayan Luma II", "Bayan Luma III", "Bayan Luma IV", "Bayan Luma V", "Bayan Luma VI", "Bayan Luma VII", "Bayan Luma VIII", "Bayan Luma IX", "Bucandala I", "Bucandala II", "Bucandala III", "Bucandala IV", "Bucandala V", "Buhay na Tubig", "Carsadang Bago I", "Carsadang Bago II", "Magdalo", "Malagasang I", "Malagasang II", "Medicion I", "Medicion II", "Pag-asa I", "Pag-asa II", "Pag-asa III", "Palico I", "Palico II", "Palico III", "Palico IV", "Pasong Buaya I", "Pasong Buaya II", "Pinagbuklod", "Poblacion I", "Poblacion II", "Poblacion III", "Poblacion IV", "Tanzang Luma I", "Tanzang Luma II", "Tanzang Luma III", "Tanzang Luma IV", "Tanzang Luma V", "Tanzang Luma VI", "Toclong I", "Toclong II"],
                "Indang": ["Poblacion", "Agus-us", "Alulod", "Banaba Cerca", "Banaba Lejos", "Buna Cerca", "Buna Lejos"],
                "Kawit": ["Poblacion", "Balsahan", "Binakayan", "Gahak", "Kaingen", "Magdalo", "Manggahan"],
                "Magallanes": ["Poblacion I", "Poblacion II", "Barangay I", "Barangay II", "Barangay III", "Kabulusan"],
                "Maragondon": ["Poblacion", "Barangay I", "Barangay II", "Barangay III", "Caingin", "Pinagsanhan"],
                "Mendez": ["Poblacion I", "Poblacion II", "Anuling Cerca", "Anuling Lejos", "Banaybanay", "Bukal"],
                "Naic": ["Poblacion", "Barangay I", "Barangay II", "Balsahan", "Bancaan", "Bucana", "Calubcob"],
                "Noveleta": ["Poblacion", "Magdiwang", "San Jose I", "San Jose II", "San Juan I", "San Juan II"],
                "Rosario": ["Poblacion", "Ligtong I", "Ligtong II", "Ligtong III", "Ligtong IV", "Muzon I", "Muzon II"],
                "Silang": ["Poblacion", "Biga I", "Biga II", "Sabutan", "San Vicente I", "San Vicente II"],
                "Tagaytay City": ["Asisan", "Bagong Tubig", "Calabuso", "Dapdap East", "Dapdap West", "Guinhawa North", "Guinhawa South", "Iruhin East", "Iruhin West", "Iruhin Central", "Kaybagal North", "Kaybagal South", "Kaybagal Central", "Maharlika East", "Maharlika West", "Mendez Crossing East", "Mendez Crossing West", "Neogan", "Patutong Malaki North", "Patutong Malaki South", "San Jose", "Silang Junction North", "Silang Junction South", "Tolentino East", "Tolentino West", "Zambal"],
                "Tanza": ["San Miguel", "Bagtas", "Biga", "Bucal", "Capipisa", "Halayhay"],
                "Ternate": ["Poblacion", "Barangay I", "Barangay II", "Barangay III", "Barangay IV", "Barangay V"],
                "Trece Martires City": ["Aguado", "Cabuco", "Cabezas", "Conchu", "De Ocampo", "Gregorio", "Inocencio", "Lapidario", "Luciano", "Osorio", "Perez", "San Agustin", "Villaville"]
            }
        },
        "Laguna": {
            municipalities: ["Alaminos", "Bay", "Biñan City", "Cabuyao City", "Calamba City", "Calauan", "Cavinti", "Famy", "Kalayaan", "Liliw", "Los Baños", "Luisiana", "Lumban", "Mabitac", "Magdalena", "Majayjay", "Nagcarlan", "Paete", "Pagsanjan", "Pakil", "Pangil", "Pila", "Rizal", "San Pablo City", "San Pedro City", "Santa Cruz", "Santa Maria", "Santa Rosa City", "Siniloan", "Victoria"],
            barangays: {}
        },
        "Batangas": {
            municipalities: ["Agoncillo", "Alitagtag", "Balayan", "Balete", "Batangas City", "Bauan", "Calaca", "Calatagan", "Cuenca", "Ibaan", "Laurel", "Lemery", "Lian", "Lipa City", "Lobo", "Mabini", "Malvar", "Mataas na Kahoy", "Nasugbu", "Padre Garcia", "Rosario", "San Jose", "San Juan", "San Luis", "San Nicolas", "San Pascual", "Santa Teresita", "Santo Tomas", "Taal", "Talisay", "Tanauan City", "Taysan", "Tingloy", "Tuy"],
            barangays: {}
        },
        "Rizal": {
            municipalities: ["Angono", "Antipolo City", "Baras", "Binangonan", "Cainta", "Cardona", "Jalajala", "Morong", "Pililla", "Rodriguez", "San Mateo", "Tanay", "Taytay", "Teresa"],
            barangays: {}
        },
        "Quezon": {
            municipalities: ["Agdangan", "Alabat", "Atimonan", "Buenavista", "Burdeos", "Calauag", "Candelaria", "Catanauan", "Dolores", "General Luna", "General Nakar", "Guinayangan", "Gumaca", "Infanta", "Jomalig", "Lopez", "Lucban", "Lucena City", "Macalelon", "Mauban", "Mulanay", "Padre Burgos", "Pagbilao", "Panukulan", "Patnanungan", "Perez", "Pitogo", "Plaridel", "Polillo", "Quezon", "Real", "Sampaloc", "San Andres", "San Antonio", "San Francisco", "San Narciso", "Sariaya", "Tagkawayan", "Tayabas City", "Tiaong", "Unisan"],
            barangays: {}
        }
    }
};

document.addEventListener('DOMContentLoaded', function() {
    const regionSelect = document.getElementById('region');
    const provinceSelect = document.getElementById('province');
    const municipalitySelect = document.getElementById('municipality');
    const barangaySelect = document.getElementById('barangay');

    // Populate Region
    const option = document.createElement('option');
    option.value = addressData.region;
    option.textContent = addressData.region;
    regionSelect.appendChild(option);

    // Auto-select region by default
    regionSelect.value = addressData.region;

    // Set region if there's an old value
    const selectedRegion = regionSelect.dataset.selectedRegion;
    if (selectedRegion) {
        regionSelect.value = selectedRegion;
    }

    // Region change event
    regionSelect.addEventListener('change', function() {
        provinceSelect.innerHTML = '<option value="">Select Province</option>';
        municipalitySelect.innerHTML = '<option value="">Select Municipality</option>';
        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';

        if (this.value) {
            Object.keys(addressData.provinces).forEach(province => {
                const option = document.createElement('option');
                option.value = province;
                option.textContent = province;
                provinceSelect.appendChild(option);
            });

            // Set province if there's an old value
            const selectedProvince = provinceSelect.dataset.selectedProvince;
            if (selectedProvince) {
                provinceSelect.value = selectedProvince;
                provinceSelect.dispatchEvent(new Event('change'));
            }
        }
    });

    // Province change event
    provinceSelect.addEventListener('change', function() {
        municipalitySelect.innerHTML = '<option value="">Select Municipality</option>';
        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';

        if (this.value && addressData.provinces[this.value]) {
            addressData.provinces[this.value].municipalities.forEach(municipality => {
                const option = document.createElement('option');
                option.value = municipality;
                option.textContent = municipality;
                municipalitySelect.appendChild(option);
            });

            // Set municipality if there's an old value
            const selectedMunicipality = municipalitySelect.dataset.selectedMunicipality;
            if (selectedMunicipality) {
                municipalitySelect.value = selectedMunicipality;
                municipalitySelect.dispatchEvent(new Event('change'));
            }
        }
    });

    // Municipality change event
    municipalitySelect.addEventListener('change', function() {
        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';

        const province = provinceSelect.value;
        const municipality = this.value;

        if (province && municipality && addressData.provinces[province].barangays && addressData.provinces[province].barangays[municipality]) {
            addressData.provinces[province].barangays[municipality].forEach(barangay => {
                const option = document.createElement('option');
                option.value = barangay;
                option.textContent = barangay;
                barangaySelect.appendChild(option);
            });

            // Set barangay if there's an old value
            const selectedBarangay = barangaySelect.dataset.selectedBarangay;
            if (selectedBarangay) {
                barangaySelect.value = selectedBarangay;
            }
        }
    });

    // Auto-trigger region change to populate provinces on page load
    regionSelect.dispatchEvent(new Event('change'));
});
</script>
