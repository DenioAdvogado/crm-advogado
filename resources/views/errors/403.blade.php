@include('errors.minimal', [
    'code' => 403,
    'title' => 'Acesso não autorizado',
    'message' => 'Você não tem permissão para acessar esta área.',
])
