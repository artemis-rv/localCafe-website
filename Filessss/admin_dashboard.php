<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
  header("Location: admin_login.html");
  exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="common.css">
  <style>
    .admin-shell {
      width: 90%;
      margin: 2rem auto;
      background: #f5e6ca;
      padding: 1rem;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(93, 64, 55, .2);
    }

    .admin-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }

    .admin-table {
      width: 100%;
      border-collapse: collapse;
      background: #fff7ed;
      border-radius: 10px;
      overflow: hidden;
    }

    .admin-table th {
      background: #a94438;
      color: #fff7ed;
      padding: .7rem;
      text-align: left;
    }

    .admin-table td {
      border-bottom: 1px solid #f0f0f0;
      padding: .6rem;
      color: #5D4037;
    }

    .admin-actions button {
      margin-right: .4rem;
      padding: .3rem .6rem;
      border: none;
      border-radius: .4rem;
      cursor: pointer;
      background: #a94438;
      color: #fff7ed;
    }

    .admin-actions button:hover {
      background: #5D4037;
    }

    .admin-controls {
      display: flex;
      gap: .5rem;
      flex-wrap: wrap;
    }

    .admin-controls input,
    .admin-controls select {
      padding: .4rem .6rem;
      border: 1px solid #a94438;
      border-radius: .4rem;
      background: #fff7ed;
      color: #5D4037;
    }
  </style>
</head>

<body>

  <header class="navigation">
    <h1 align="center">B&B</h1>
    <h2 align="center">Brew & Beyond</h2>
    <nav>
      <ul>
        <div class="right" id="adminRight">
          <?php echo "Welcome, " . htmlspecialchars($_SESSION['admin_name']) . "! "; ?>
          <button style="background-color: #a13b19ff; border-radius: 0.7em; padding: 0.5em; color: #fff;" onclick="window.location.href='admin_logout.php'">Logout</button>
        </div>

      </ul>
    </nav>
  </header>

  <div class="admin-shell">
    <div class="admin-bar">
      <h2 style="margin:0">Users</h2>
      <div class="admin-controls">
        <!-- <input id="searchUser" placeholder="Search by name/email">
        <button id="searchBtn">Search</button>
        <select id="filterStatus">
          <option value="">All</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select> -->
        <button id="addUserBtn">Add User</button>
        <button id="addUserBtn" onclick="window.location.href='admin_logout.php'">Back to Home</button>
      </div>
    </div>

    <table class="admin-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Role</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="usersBody"></tbody>
    </table>
  </div>

  <template id="rowTpl">
    <tr>
      <td class="id"></td>
      <td class="name"></td>
      <td class="email"></td>
      <td class="phone"></td>
      <td class="role"></td>
      <td class="status"></td>
      <td class="admin-actions">
        <button class="edit">Edit</button>
        <!-- <button class="toggle">Toggle Status</button> -->
        <button class="delete">Delete</button>
      </td>
    </tr>
  </template>

  <script>
    // Fetch all users and populate the table
    async function loadUsers() {
      try {
        const response = await fetch("fetch_users.php");
        const users = await response.json();
        const tbody = document.getElementById("usersBody");
        const tpl = document.getElementById("rowTpl").content;
        tbody.innerHTML = ""; // Clear table

        users.forEach(user => {
          const row = tpl.cloneNode(true);
          row.querySelector(".id").textContent = user.id;
          row.querySelector(".name").textContent = user.name;
          row.querySelector(".email").textContent = user.email;
          row.querySelector(".phone").textContent = user.phone;
          row.querySelector(".role").textContent = user.role;
          row.querySelector(".status").textContent = user.status;

          // === EDIT BUTTON ===
          row.querySelector(".edit").addEventListener("click", async () => {
            const newName = prompt("Enter new name:", user.name);
            const newRole = prompt("Enter new role (admin/user):", user.role);
            if (!newName || !newRole) return;

            const formData = new FormData();
            formData.append("id", user.id);
            formData.append("name", newName.trim());
            formData.append("role", newRole.trim());

            const res = await fetch("update_user.php", {
              method: "POST",
              body: formData
            });
            const result = await res.json();
            alert(result.message);
            if (result.success) loadUsers();
          });

          // === TOGGLE STATUS BUTTON ===
          // row.querySelector(".toggle").addEventListener("click", async () => {
          //   const formData = new FormData();
          //   formData.append("id", user.id);

          //   const res = await fetch("toggle_status.php", {
          //     method: "POST",
          //     body: formData
          //   });
          //   const result = await res.json();
          //   alert(result.message);
          //   if (result.success) loadUsers();
          // });

          // === DELETE BUTTON ===
          row.querySelector(".delete").addEventListener("click", async () => {
            if (!confirm(`Are you sure you want to delete ${user.name}?`)) return;

            const formData = new FormData();
            formData.append("id", user.id);

            const res = await fetch("delete_user.php", {
              method: "POST",
              body: formData
            });
            const result = await res.json();
            alert(result.message);
            if (result.success) loadUsers();
          });

          tbody.appendChild(row);
        });
      } catch (error) {
        console.error("Error loading users:", error);
      }
    }

    // Initial load
    loadUsers();



    // === ADD USER BUTTON ===
    document.getElementById("addUserBtn").addEventListener("click", async () => {
      const name = prompt("Enter user's name:");
      const email = prompt("Enter user's email:");
      const phone = prompt("Enter user's phone:");
      const role = prompt("Enter role (admin/user):");

      if (!name || !email || !phone || !role) {
        alert("All fields are required!");
        return;
      }

      // Basic frontend validation for email
      const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
      if (!email.match(emailPattern)) {
        alert("Invalid email format!");
        return;
      }

      const formData = new FormData();
      formData.append("name", name.trim());
      formData.append("email", email.trim());
      formData.append("phone", phone.trim());
      formData.append("role", role.trim());

      try {
        const res = await fetch("add_user.php", {
          method: "POST",
          body: formData
        });
        const result = await res.json();
        alert(result.message);
        if (result.success) loadUsers(); // Refresh table
      } catch (error) {
        console.error("Error adding user:", error);
      }
    });

  </script>
</body>

</html>