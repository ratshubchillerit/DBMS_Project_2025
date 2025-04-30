<?php
session_start();
include "db.php"; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $user_id = $_POST['user_id'];
  $password = $_POST['password'];
  $user_type = $_POST['user_type'];

  // Query the database to check if the user ID and user type exist
  $sql = "SELECT * FROM users WHERE userID = ? AND userType = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("is", $user_id, $user_type);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    // Fetch the user data
    $user = $result->fetch_assoc();

    // Verify password
    if ($user['password'] == $password) {
      // Set session variables for the user
      $_SESSION['user_id'] = $user['userID'];
      $_SESSION['user_type'] = $user['userType'];

      // Redirect to the relevant dashboard
      if ($user_type == 'Farm Manager') {
        header("Location: farmManager.php");
      } elseif ($user_type == 'Farmer') {
        header("Location: farmer.php");
      } elseif ($user_type == 'Customer') {
        header("Location: customer.php");
      } elseif ($user_type == 'Retailer') {
        header("Location: retailer.php");
      } elseif ($user_type == 'Wholesaler') {
        header("Location: wholesaler.php");
      } elseif ($user_type == 'Government Officer') {
        header("Location: govtOfficer.php");
      } elseif ($user_type == 'Cold Storage Manager') {
        header("Location: coldStorageManager.php");
      } elseif ($user_type == 'Admin') {
        header("Location: admin.php");
      }
      exit();
    } else {
      $error = "Invalid credentials!";
    }
  } else {
    $error = "User not found!";
  }
  $stmt->close();
  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Meat Supply Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body
    class="min-h-screen bg-gradient-to-br from-red-100 via-white to-red-200 flex items-center justify-center font-sans">
    <div class="bg-white shadow-2xl rounded-2xl p-8 w-full max-w-md">
        <h2 class="text-3xl font-bold text-center text-red-700 mb-6">Login to Dashboard</h2>

        <!-- Show error if login fails -->
        <?php if (isset($error)): ?>
        <div class="text-red-500 text-center mb-4"><?= $error ?></div>
        <?php endif; ?>

        <form class="space-y-5" method="POST">
            <div>
                <label class="block text-gray-700 font-medium mb-1">User ID</label>
                <input type="text" name="user_id" placeholder="Enter your ID"
                    class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-400"
                    required />
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-1">Password</label>
                <input type="password" name="password" placeholder="Enter your password"
                    class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-400"
                    required />
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-1">User Type</label>
                <select name="user_type"
                    class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-400"
                    required>
                    <option value="">Select user type</option>
                    <option>Farm Manager</option>
                    <option>Farmer</option>
                    <option>Customer</option>
                    <option>Retailer</option>
                    <option>Wholesaler</option>
                    <option>Government Officer</option>
                    <option>Cold Storage Manager</option>
                    <option>Admin</option>
                </select>
            </div>

            <button type="submit"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-xl transition duration-300">
                Login
            </button>
        </form>

        <p class="mt-4 text-center text-sm text-gray-500">
            Forgot your password?
            <a href="#" class="text-red-500 hover:underline">Click here</a>
        </p>
    </div>
</body>

</html>