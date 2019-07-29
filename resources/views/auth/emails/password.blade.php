Haz click aquí para reestablecer tu password: 
</br>
<a href="{{ $link = url('password/reset', $token).'?email='.urlencode($user->getEmailForPasswordReset()) }}"> Reestablecer Password </a>