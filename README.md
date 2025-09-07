# FoodLink – Community Food Sharing (PHP + MySQL)

<!-- <img src="img/coverimage.jpeg"> -->
<p>  The basic concept of this project  Food Waste Management is to collect theexcess/leftover food from donors such as hotels, restaurants, marriage halls, etc and distribute to  the  needy people .</p>
<h2>Tools and Technologies</h2> 
<ul>
 <li>Frontend : HTML, CSS,  JavaScript</li>
 <li>Backend  : php</li>
 <li>webserver: xampp server</li>
 <li>Database: MySQL </li>
</ul>

 <h2>What it does</h2>
 <ul>
  <li>Single account to post food and claim food</li>
  <li>Session-based login; stays signed in until logout</li>
  <li>SweetAlert2 feedback for login, signup, posting, claiming</li>
  <li>PHPMailer via Gmail App Password for claim/contact emails</li>
  <li>Unified dashboard: Available, Your Foods, My Claimed</li>
  <li>Search in Available list; clean, responsive UI</li>
 </ul>
   <br>
    <p>The User module is designed for people who wish to donate their excess or leftover food to help reduce food wastage.The User module is responsible for accepting food donations from users who have excess food, such as marriage halls, restaurants, or individuals.The module provides users with the ability to register, login, and donate food. Users can select the type and quantity of food they want to donate, and the system will match their donation with the nearest needy people or organizations.The module also allows users to view their donations.The User module provides the information to the Admin module for further processing.
   </p><br>
   <p>
      The Administrator module is for trusts, NGOs, and charities that are registered on the platform. The Admin module is designed for system administrators who manage the food distribution process. The Admin module receives information about the food donation from the User module and lists it for NGOs and charities to choose from.Admins can view and manage the list of donations received, including the type and quantity of food donated. NGOs and charities can select the food donation they need from the Admin module and request a pickup to the Delivery module.The Admin module is responsible for tracking the requests and keeping track of which organizations have taken which donations
   </p><br>
    <p>The Delivery Person module is for individuals who wish to participate in the food donation process by providing pickup and delivery services. Delivery personnel can register themselves on the platform .The Delivery Person module provides pickup and drop-off services for NGOs and charities who have requested a food donation.The Delivery Person module shows the pickup location and drop location of the food donation.
    </p><br>
    <p>Overall, FoodLink helps reduce food waste by letting donors post surplus food and community members claim it quickly.
    </p>
    <h3>User </h3>
   <!-- <img src="img/User-module.jpg"> -->
    <img src="img/mobile.jpg">
    <h3>Admin </h3>
    <img src="img/Admin.jpg">
     <h3>Delivery </h3>
    <img src="img/Delivery_module.jpg">
    <h3>features:</h3>
    <ul><li>Mobile Screen friendly website.</li>
      <li>chatbot support</li>
      <li>Secure Login</li>
      </ul>
      <h2>Mobile Screen friendly website.</h2>
      <img src="img/responsive.gif">
      <h2>chatbot support</h2>
      <img src="img/chatbotsupport.jpg">
      <h2>Secure Login</h2>
      <img src="img/hash-flow.png">
      <h2>Setup</h2>
      <ol>
       <li>Copy the folder to XAMPP htdocs: <code>C:\xampp\htdocs\php-practice</code></li>
       <li>Database: create database <code>sample</code> and import <code>sample (1).sql</code> (or <code>database/demo.sql</code>)</li>
       <li>Configure MySQL in <code>connection.php</code> (host 127.0.0.1, port 4307, db name sample)</li>
       <li>Install Composer dependencies (PHPMailer): <code>C:\xampp\php\php.exe C:\composer\composer.phar install</code></li>
       <li>Copy <code>mail_secrets.php.example</code> to <code>mail_secrets.php</code>, set Gmail and App Password</li>
       <li>Visit <code>http://localhost/php-practice/index.html</code></li>
      </ol>

      <h2>Security</h2>
      <ul>
        <li>Never commit <code>mail_secrets.php</code>. It’s in <code>.gitignore</code>.</li>
        <li>PHPMailer uses Gmail App Password; rotate if leaked.</li>
      </ul>

<h2>View</h2>
Local: <code>http://localhost/php-practice/</code>
