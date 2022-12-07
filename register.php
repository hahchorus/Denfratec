<?php

require_once "config.php";
 
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
 
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    if(empty(trim($_POST["username"]))){
        $username_err = "Por favor, ingrese un nombre de usuario.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "El nombre de usuario solo puede contener letras, numeros y guiones bajos.";
    } else{
        
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = $mysqli->prepare($sql)){
            
            $stmt->bind_param("s", $param_username);
            
            
            $param_username = trim($_POST["username"]);
            
            if($stmt->execute()){
                
                $stmt->store_result();
                
                if($stmt->num_rows == 1){
                    $username_err = "Este nombre de usuario ya esta en uso.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "UPS! Algo salio mal. Por favor, intentelo de nuevo mas tarde.";
            }

            $stmt->close();
        }
    }
    
    if(empty(trim($_POST["password"]))){
        $password_err = "Por favor ingrese una clave.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "La clave debe tener al menos 6 caracteres.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Por favor confirme la clave.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "La clave no coincidio.";
        }
    }
    
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        
        $sql = "INSERT INTO users (username, password, tipoUser) VALUES (?, ?,?)";
         
        if($stmt = $mysqli->prepare($sql)){
            
            $stmt->bind_param("ssi", $param_username, $param_password, $_POST["tipoUser"]);
            
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); 
            
            if($stmt->execute()){
                
                header("location: login.php");
            } else{
                echo "UPS! Algo salio mal. Por favor, intentelo de nuevo mas tarde.";
            }

            
            $stmt->close();
        }
    }
    
    
    $mysqli->close();
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; } 
        .wrapper{ width: 360px; padding: 20px; }
        select {
            background: gold;
            border-radius: 4px;
            border: gold;
            width: 100px;
            height: 40px;
            text-align: center;
            padding: 3px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Registrar cuenta</h2>
        <p>Llena el formulario para crear tu cuenta..</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Escribe nuevamente tu contraseña</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div>
            <br><br>
            <label>Selecciona un tipo de cuenta</label>
            <select name="tipoUser">
                <option value="1">Soporte</option>
                <option value="1" selected>Recursos humanos</option>
                <option value="2" selected>Finanzas</option>
                <option value="2" selected>Director</option>
            </select>   
            <br><br><br>         
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Registrar">
            </div>
            <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí.</a>.</p>
        </form>
    </div>    
</body>
</html>