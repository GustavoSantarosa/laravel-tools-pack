
<h3 align="center">laravel-tools-pack</h3>


## ð§ Sobre <a name = "about"></a>

Este pacote de ferramentas para laravel fornece algumas ferramentas uteis que ajudam no desenvolvimento e os tornam mais ageis.

## ð Para utilizar o pack

Caso o projeto esteja privado, voce vai precisar introduzir o seguinte trecho de json no seu composer.json:
```
"repositories": [
  {
    "type": "git",
    "url": "https://github.com/GustavoSantarosa/laravel-tools-pack.git"
  }
]
```

Caso esteja publico, basta utilizar o comando:
```
composer require gustavosantarosa/laravel-tool-pack
```

Pronto, ja Ã© para estar funcionando.

## ð Utilizando

Nele existem algumas ferramentas uteis.
- BaseController
  - Retornos atraves da classe DTO, para padronizar o retorno.
  - Classes prontas para as rotas index, show e destroy.
- BaseModel
  - Sync para hasMany, atualmente no laravel nÃ£o existe.
  - Where dinamico, passado pelo consumidor da api.
  - OrWhere dinamico, passado pelo consumidor da api.
  - between dinamico, passado pelo consumidor da api.
- BaseService
  - Uma estrutura pronta para as rotas Rest utilizando querybuilder

## âï¸ Utilizado

- [php](https://www.php.net/) - linguagem
- [laravel](https://laravel.com/) - framework
- [Laravel-query-builder](https://spatie.be/docs/laravel-query-builder/v3/installation-setup) - Auxilio de suporte ao framework

## âï¸ Autor

- [@Luis Gustavo Santarosa Pinto](https://github.com/GustavoSantarosa) - Idea & Initial work

