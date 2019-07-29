<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">


</br>
</br>
</br>
<div class="container">
    <div class="row">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="text-center">
                        <h3><i class="glyphicon glyphicon-lock fa-4x"></i></h3>
                        
                        <h2 class="text-center">Restaurar password</h2>
                        <p>Ingresa tu correo y la nueva password a utilizar en la aplicación.</p>
                        <div class="panel-body">
                              
                        <form action="/password/reset" method="post">
                            <input type="hidden" name="token" value="{{ $token }}">
                            <fieldset>
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-envelope color-blue"></i></span>
                                        <input name="usu_email" placeholder="email" class="form-control" type="email" required="">
                                    </div>
                                    </br>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock color-blue"></i></span>
                                        <input name="password" placeholder="password" class="form-control" type="password" required="">
                                    </div>
                                    </br>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock color-blue"></i></span>
                                        <input name="password_confirmation" placeholder="confirma password" class="form-control" type="password" required="">
                                    </div>

                                </div>
                                    <div class="form-group">
                                        <input class="btn btn-lg btn-primary btn-block" value="Confirmar" type="submit">
                                    </div>
                                </fieldset>
                              </form>
                              
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    @if (isset($))
                        {{dfsdf}}
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>