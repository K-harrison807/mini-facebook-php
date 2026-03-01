<?php
session_start();
include('Database.php');
mysqli_select_db($conn, $databaseName);

// Create cart if not exists
if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = [];
}

// Get product ID
if(isset($_GET['id'])){
    $_SESSION['selected_product'] = $_GET['id'];
}

if(isset($_SESSION['selected_product'])){
    $id = $_SESSION['selected_product'];

    $query = "SELECT * FROM products WHERE productID = '$id'";
    $result = mysqli_query($conn, $query);
    $product = mysqli_fetch_assoc($result);
}
$message="";
// ADD
if(isset($_POST['add'])){
    if(!isset($_SESSION['cart'][$id])){
        $_SESSION['cart'][$id] = 0;
    }
   
    $quantityQuery=mysqli_query($conn,"SELECT Quantity FROM Products WHERE productID='$id'");
    $productQuantity=mysqli_fetch_assoc($quantityQuery);

    if($_SESSION['cart'][$id]<$productQuantity['Quantity']){
         $_SESSION['cart'][$id]++;
        
    }
    else{
        $message="Out of stock";
    }
    // header("Location: userAccount.php?id=$id");
   // exit();
}

// MINUS
if(isset($_POST['minus'])){
    if(isset($_SESSION['cart'][$id]) && $_SESSION['cart'][$id] > 0){
        $_SESSION['cart'][$id]--;
    }
     //header("Location: userAccount.php?id=$id");
   // exit();
}

// Get quantity safely
$cartQty = 0;
if(isset($_SESSION['cart'][$id])){
    $cartQty = $_SESSION['cart'][$id];
}
$priceQuery=mysqli_query($conn,"SELECT Price FROM Products WHERE productID='$id'");
$price=mysqli_fetch_assoc($priceQuery);

$Total= $price['Price'] * $cartQty;
$_SESSION["total"]=$Total;

if(isset($_POST["checkout"])){
    header("Location:checkout.php?id=$id");
}
if(isset($_POST["backBtn"])){
    header("Location:shop.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <style>
        .checkoutBtn{
            color: greenyellow;
            width: 350px;
            height: 80px;
        }
        .addBtn{
            background-color: blue;
        }
        .minusBtn{
            background-color: red;
        }
        .buttn{
            background-color: orange;
            color: black;
        }
        .total{
            color: greenyellow;
        }
        .msgColor{
            color: red;
        }
    </style>
</head>
<body>
    <form method="post">
    <button class="buttn" name="backBtn">Back</button>
    </form>
    <br>
    <br>


    <body>

<?php if(isset($product)) { ?>

    <img src="storeImages/<?php echo $product['Image']; ?>" width="150">
    <p>Price: R<?php echo $product['Price']; ?></p>

<?php } ?>

</body>
Quantity:<?php echo $cartQty;?><br><br>
<span class="msgColor"><?php echo  $message; ?></span>
<form action="userAccount.php?id=<?php echo $id; ?>" method="POST">
<button type="submit" name="add" class="addBtn">+</button>
<button type="submit" name="minus" class="minusBtn">-</button><br>
<b>Total: <span class="total">R<?php echo $Total?></b></span><br><br><br>
<button type="submit" name="checkout" class="buttn">Checkout</button>
</form>
<br><br>

<br><br>


    
</body>
</html>