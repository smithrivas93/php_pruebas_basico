<?php
class Acceso{
  protected $user;
  protected $pass;
  protected $email;

  public function __construct($usuario,$password,$email){
    $this->user = $usuario;
    $this->pass = $password;
    $this->email = $email;
  }

  public function Login(){
    // Conexion a la DB.
    $db = new Conexion();
    // Consulta.
    $sql = $db->query("SELECT nombre,password FROM usuarios
      WHERE nombre = '$this->user' AND password = '$this->pass'");
    // Se llama la instancia recorrer para mostrar los datos arrojados por la consulta.
    $data = $db->recorrer($sql);
    //$data['nombre'];
    //$data['password'];
    if ($data['nombre'] === $this->user AND $data['password'] === $this->pass) {
      session_start();
      $_SESSION['user'] = $this->user;
      header('location: acceso.php');
    } else {
      session_start();
      $_SESSION['error'] = 'Datos incorrectos';
      header('location: index.php');
    }

  }

  public function Registro(){
    // Conexion a la DB.
    $db = new Conexion();
    // Consultar datos ingresados en la Db.
    $sql = $db->query(" SELECT nombre,email FROM usuarios WHERE nombre='$this->user' OR email='$this->email'; ");
    //Pasar los datos a un Array.
    $existe = $db->recorrer($sql);

    //Si los datos ingresados no son iguales a los de la DB entonces insertar los datos en la BD.
    if ($existe['nombre'] != $this->user and $existe['email'] != $this->email) {
      $db->query(" INSERT INTO usuarios (nombre, email, password)
                          VALUES ('$this->user','$this->email','$this->pass'); ");
                          //echo "Datos insertados";
                          session_start();
                          $_SESSION['registroExitoso'] = 'Te has registrado exitosamente.';
                          header('location: index.php');
  //Si el nombre ingresado y el email ya esta en la Db redireccionar a formulario de registro e informar error.
  }else if ($existe['nombre'] == $this->user and $existe['email'] == $this->email){
    session_start();
    $_SESSION['registroError'] = 'ERROR: el nombre de usuario y el email ya existen, has olvidado tu cuenta ? <a href="recuperar.php"> Recuperala!</a>';
    header('location: registro.php');
        //Si el nombre ingresado ya esta en la Db redireccionar a formulario de registro e informar error.
        }else if($existe['nombre'] == $this->user){
        session_start();
        $_SESSION['registroError'] = 'ERROR: el nombre de usuario ya existe, digita uno distinto.';
        header('location: registro.php');
            //Si el email ingresado ya esta en la Db redireccionar a formulario de registro e informar error.
            }else if($existe['email'] == $this->email){
            session_start();
            $_SESSION['registroError'] = 'ERROR: la direccion de email registrada ya existe, digita uno distinto.';
            header('location: registro.php');
            }
  }

  public function Clave_perdida(){
    $db = new Conexion();
    $sql = $db->query(" SELECT email FROM usuarios WHERE email='$this->email'; ");
    $data = $db->recorrer($sql);
    if ($data['email'] == $this->email) {
      include('includes/class.generar_pass.php');
      $passw = new Generarpass();
      //Se le pasa por parametro la longitud de la contraseña.
      $password = $passw->Nueva_pass(10);
      //Se actualiza la contraseña.
      $db->query(" UPDATE usuarios SET password='$password' WHERE email='$this->email'; ");
      //3 parametros/Correo/Asunto/Conenido del correo.
      mail($this->email,'Cambio de contraseña',"Su contraseña ha sido cambiada, nueva contraseña: $password");
      session_start();
      $_SESSION['recuperarExitoso'] = 'La nueva contraseña ha sido enviada a tu correo electronico.';
      header('location: index.php');
    } else {
      session_start();
      $_SESSION['recuperarError'] = 'El email que has ingresado no se encuentra registrado.';
      header('location: recuperar.php');
    }

  }
}
?>
