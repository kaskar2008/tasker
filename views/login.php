<section page>
    <h1 huge tac>Login page</h1>
    <form login method="POST" action="/login">

        <?php if($errors) {?>
        <div form-group<?php echo isset($errors->email)==1 ? ' has-error' : '' ?>>
            <label for="email" control-label>E-Mail Address</label>
            <div>
                <input id="email" type="email" form-control name="email" value="<?php echo $_POST["email"] ?>" autofocus>

                <?php if (isset($errors->email)==1) {?>
                    <span help-block>
                        <strong><?php echo $errors->email ?></strong>
                    </span>
                <?php } ?>
            </div>
        </div>
        <?php } ?>

        <div form-group<?php echo isset($errors->password) ? ' has-error' : '' ?>>
            <label for="password" control-label>Password</label>
            <div>
                <input id="password" type="password" form-control name="password">

                <?php if (isset($errors->password)) {?>
                    <span help-block>
                        <strong><?php echo $errors->password ?></strong>
                    </span>
                <?php } ?>
            </div>
        </div>

        <div form-group>
            <div>
                <button type="submit" btn btn-primary>
                    Login
                </button>
            </div>
        </div>
    </form>
</section>