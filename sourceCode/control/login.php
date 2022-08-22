<?PHP

use Nette\Utils\Strings;

session_start();
header("Content-Type: text/json");
include 'connect.php';

$username = $_POST['username']; //post获得用户名表单值
$password = $_POST['password']; //post获得用户密码单值

if ($username && $password) {
    $sql = "select * from user where username = '$username'"; //检测数据库是否有对应的username
    $result = mysqli_query($account, $sql); //执行sql
    if ($result && mysqli_num_rows($result) > 0) {
        $dbpwd = strval(mysqli_fetch_assoc($result)['password']); //取出数据库密码
        if (password_verify($password, $dbpwd)) {
            $_SESSION['username'] = $username;

            echo "{\"result\":\"登陆成功\"}";
            return;
        } else {
            echo "{\"result\":\"密码错误\"}";
            return;
        }
    } else {
        echo "{\"result\":\"用户名不存在\"}";
        return;
    }
} else { //如果用户名或密码有空
    echo "{\"result\":\"请填写表单\"}";
    return;
}
