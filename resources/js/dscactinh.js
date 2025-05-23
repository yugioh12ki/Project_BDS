// main.js
const provinceSelect = document.getElementById('province');
const districtSelect = document.getElementById('district');
const wardSelect = document.getElementById('ward');

async function fetchProvinces() {
  const res = await fetch('https://provinces.open-api.vn/api/?depth=1');
  const provinces = await res.json();
  provinceSelect.innerHTML = '<option value="">Chọn Tỉnh/Thành phố</option>';
  provinces.forEach(p => {
    const option = document.createElement('option');
    option.value = p.name; // Giữ name làm value để submit form
    option.setAttribute('data-code', p.code); // Thêm code để gọi API
    option.textContent = p.name;
    provinceSelect.appendChild(option);
  });
}

async function fetchDistricts(provinceName) {
  const selectedOption = Array.from(provinceSelect.options).find(
    option => option.value === provinceName
  );
  if (!selectedOption) return;

  const provinceCode = selectedOption.getAttribute('data-code');
  if (!provinceCode) return;

  const res = await fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`);
  const data = await res.json();
  
  districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
  wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
  
  data.districts.forEach(d => {
    const option = document.createElement('option');
    option.value = d.name; // Giữ name làm value để submit form
    option.setAttribute('data-code', d.code); // Thêm code để gọi API
    option.textContent = d.name;
    districtSelect.appendChild(option);
  });
}

async function fetchWards(districtName) {
  const selectedOption = Array.from(districtSelect.options).find(
    option => option.value === districtName
  );
  if (!selectedOption) return;

  const districtCode = selectedOption.getAttribute('data-code');
  if (!districtCode) return;

  const res = await fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`);
  const data = await res.json();
  
  wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
  data.wards.forEach(w => {
    const option = document.createElement('option');
    option.value = w.name;
    option.textContent = w.name;
    wardSelect.appendChild(option);
  });
}

// Sự kiện khi chọn tỉnh/thành phố
provinceSelect.addEventListener('change', () => {
  const provinceName = provinceSelect.value;
  if (provinceName) {
    fetchDistricts(provinceName);
  } else {
    districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
    wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
  }
});

// Sự kiện khi chọn quận/huyện
districtSelect.addEventListener('change', () => {
  const districtName = districtSelect.value;
  if (districtName) {
    fetchWards(districtName);
  } else {
    wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
  }
});

// Load danh sách tỉnh khi trang được tải
document.addEventListener('DOMContentLoaded', () => {
  fetchProvinces();
});
