<script src="/script/loginCheck.js"></script>
<div class="changeModal" id="changeModal">
    <ul class="modal-content">
        <i class="iconfont quit" onclick="changeModalQuit()">&#xe620;</i>
        <li id="login">
            <h3>登录</h3>
            <form id="form1" method="post">
                <ul>
                    <li class="loadings" onselectstart="return false">
                        <div class="loading"></div>
                    </li>
                    <li class="github-login"><input type="button" onclick="githubLogin()" class="github iconfont" value="&#xe604; 使用GitHub登录" tabindex="1"></li>
                    <li><input type="text" name="username" id="username" value="" placeholder="用户名" tabindex="2"></li>
                    <li><input type="password" name="password" id="password" value="" placeholder="密码" tabindex="3"></li>
                    <li><label onselectstart="return false"><input type="checkbox" id="memory" tabindex="4" onclick="memoryClick()"><span>记住我</span></label></li>
                    <li><input type="button" value="登录" onclick="LoginUser()" tabindex="5"></li>
                </ul>
            </form>
            <div class="modal-content-footer">
                <p><a href="error/error.html" tabindex="6">忘记了密码?</a></p>
                <p>还没有注册？<a href="javascript:void(0);" onclick="toregister()" tabindex="7">注册</a></p>
            </div>
        </li>
        <li id="register">
            <h3>注册</h3>
            <form id="form2" method="post">
                <ul>
                    <li class="loadings" onselectstart="return false">
                        <div class="loading"></div>
                    </li>
                    <li class="github-login"><input type="button" onclick="githubLogin()" class="github iconfont" value="&#xe604; 使用GitHub登录" tabindex="1"></li>
                    <li><input type="text" name="username" value="" placeholder="用户名" autocomplete="new-password" tabindex="2" title="3~12位字符，只能包含英文字母、数字、下划线"></li>
                    <li><input type="password" name="password" value="" placeholder="密码" autocomplete="new-password" tabindex="3" title="6~18位字符，允许输入英文字母、数字、任意可见字符，但必需包含英文大小写和数字"></li>
                    <li><input type="password" name="repPassword" value="" placeholder="确认密码" autocomplete="new-password" tabindex="4" title="请再次输入密码"></li>
                    <li><input type="button" value="注册并登录" onclick="RegisterUser()" tabindex="5"></li>
                </ul>
            </form>
            <div class="modal-content-footer">
                <p>已经注册过了？<a href="javascript:void(0);" onclick="tologin()" tabindex="6">登录</a></p>
            </div>
        </li>
    </ul>
</div>
<div class="header">
    <div class="show">
        <h2 id="site-title"></h2>
        <?php
        if (isset($_SESSION["username"])) {
        ?>
            <div class="online">
                <?php
                $headImgSQL = "SELECT *FROM user WHERE username = '" . $_SESSION["username"] . "'";
                $myHeadImg = mysqli_fetch_assoc(mysqli_query($account, $headImgSQL));
                ?>
                <button onclick="btnUser(this)"><img src="<?= $myHeadImg['headImg'] ?>" alt=""><span><?= $_SESSION["username"] ?></span></button>
                <ul class="select-menu">
                    <li><a href="/userinfo/userinfo.php?username=<?= $_SESSION["username"] ?>"><span class="iconfont">&#xe611;</span><span class="text">我的资料</span></a></li>
                    <li class="separate"></li>
                    <li><a class="quit" href="<?php echo $_SERVER['REQUEST_URI']; ?>"><span class="iconfont">&#xe606;</span><span class="text">退出</span></a></li>
                </ul>
            </div>

        <?php } else { ?>
            <div class="not-login">
                <ul>
                    <li><a id="toregister" onclick="toregister()">注册</a></li>
                    <li><a id="tologin" onclick="tologin()">登录</a></li>
                </ul>
            </div>
        <?php } ?>
        <div class="search">
            <input type="text" placeholder="输入需要查找的内容" id="searchText"><input type="button" value="&#xe8b9;" class="iconfont" id="searchBut" onclick="showSearch()">
        </div>
    </div>
</div>