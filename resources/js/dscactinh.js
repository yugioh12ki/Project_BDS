// main.js
const provinceSelect = document.getElementById('province');
const districtSelect = document.getElementById('district');
const wardSelect = document.getElementById('ward');

async function fetchProvinces() {
  const res = await fetch('https://provinces.open-api.vn/api/?depth=1');
  const provinces = await res.json();
  provinces.forEach(p => {
    const option = document.createElement('option');
    option.value = p.code;
    option.textContent = p.name;
    provinceSelect.appendChild(option);
  });
}

async function fetchDistricts(provinceCode) {
  const res = await fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`);
  const data = await res.json();
  districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
  wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
  data.districts.forEach(d => {
    const option = document.createElement('option');
    option.value = d.code;
    option.textContent = d.name;
    districtSelect.appendChild(option);
  });
}

async function fetchWards(districtCode) {
  const res = await fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`);
  const data = await res.json();
  wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
  data.wards.forEach(w => {
    const option = document.createElement('option');
    option.value = w.code;
    option.textContent = w.name;
    wardSelect.appendChild(option);
  });
}

provinceSelect.addEventListener('change', () => {
  const provinceCode = provinceSelect.value;
  if (provinceCode) fetchDistricts(provinceCode);
});

districtSelect.addEventListener('change', () => {
  const districtCode = districtSelect.value;
  if (districtCode) fetchWards(districtCode);
});

fetchProvinces();
