<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dream Home Real Estate Group 4</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div>
            <h1>Dream Home Real Estate Group 4</h1>
            <h2>Comp 214 (Sec 401)</h2>
        </div>
    </header>
    <nav>
        <div>
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="staff.html">Staff Main Menu</a></li>
                <li><a href="branch.html">Branch Main Menu</a></li>
                <li><a href="client.html">Client Main Menu</a></li>
            </ul>
        </div>
    </nav>
    <main>
        <!-- fetch list from database -->
        <div>
            <h3>Client Main Menu</h3>
            <form action="">
                <label for="clientList">Clients:</label>
                <select name="clientList" id="clientList">
                  <option></option>
                </select>

                <!-- <a href="hireStaff.html"><input type="submit" value="Edit"></a> 
                 the form will basically be the same, i didnt make a separate page for editing the employee maybe we can use javascript to so that we only need one form,-->
                <div class="marginTop">
                    <a href="clientForm.html"><button>Edit Client</button></a>
                    <a href="clientForm.html"><button>New Client</button></a>
                </div>
              </form>
        </div>
    </main>
    <aside>
        <div></div>
    </aside>
    <footer>
        <ul>
            <li>Julian Quan Fun</li>
            <li>Serhat Tasdelen</li>
            <li>Moath Zaghdad</li>
        </ul>
    </footer>
</body>
</html>