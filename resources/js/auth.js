<script>
    const isAdmin = true;
  if (isAdmin) {
    document.getElementById("adminMenu").style.display = "block"
  }

  function logout() {
    // Xóa thông tin đăng nhập
    // localStorage.removeItem('isAdmin');
    alert("Đã đăng xuất!")
    // location.reload(); hoặc chuyển hướng
  }
</script>
