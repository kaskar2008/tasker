<section page>
    <h1 huge tac>Create new user</h1>
    <form register method="POST" action="/register">

        <div form-group<?php echo isset($errors->name)==1 ? ' has-error' : '' ?>>
            <label for="name" control-label>Name</label>
            <div>
                <input id="name" type="text" form-control name="name" value="<?php echo $_POST["name"] ?>" autofocus>

                <?php if (isset($errors->name)==1) {?>
                    <span help-block>
                        <strong><?php echo $errors->name ?></strong>
                    </span>
                <?php } ?>
            </div>
        </div>

        <div form-group<?php echo isset($errors->email)==1 ? ' has-error' : '' ?>>
            <label for="email" control-label>E-Mail Address</label>
            <div>
                <input id="email" type="email" form-control name="email" value="<?php echo $_POST["email"] ?>">

                <?php if (isset($errors->email)==1) {?>
                    <span help-block>
                        <strong><?php echo $errors->email ?></strong>
                    </span>
                <?php } ?>
            </div>
        </div>

        <div form-group<?php echo isset($errors->name)==1 ? ' has-error' : '' ?>>
            <label for="password" control-label>Password</label>
            <div>
                <input id="password" type="password" form-control name="password">

                <?php if (isset($errors->password)==1) {?>
                    <span help-block>
                        <strong><?php echo $errors->password ?></strong>
                    </span>
                <?php } ?>
            </div>
        </div>

        <div form-group>
            <div>
                <button type="submit" btn btn-primary>
                    Register
                </button>
            </div>
        </div>
    </form>
</section>