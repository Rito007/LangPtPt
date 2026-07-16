# Bagisto Lang pt_PT

Tradução para Português Europeu (pt_PT) do Bagisto 2.4.x.

## Instalação

```bash
# 1. Clonar o package para dentro do projeto Bagisto
git clone https://github.com/Rito007/bagisto-lang-pt-pt packages/Rito007/LangPtPt

# 2. Registrar o Service Provider em bootstrap/providers.php
```

```php
// bootstrap/providers.php
use Rito007\LangPtPt\Providers\LangPtPtServiceProvider;

return [
    // ...
    LangPtPtServiceProvider::class,
];
```

```bash
# 3. Publicar as traduções
php artisan vendor:publish --tag=bagisto-lang-pt-pt
```

## Configurar a localidade

Em `config/app.php`:

```php
'locale' => 'pt_PT',
```

## Atualizar

```bash
cd packages/Rito007/LangPtPt
git pull
php artisan vendor:publish --tag=bagisto-lang-pt-pt --force
```

## Demo Products (pt_PT)

Se instalaste o Bagisto com `php artisan bagisto:install --demo-samples`, podes adicionar as traduções pt_PT dos produtos demo:

```bash
php artisan bagisto:seed-demo-ptpt
```

Isto insere traduções pt_PT para nomes, descrições, meta dados, atributos e opções de atributos nos produtos demo. Após correr o comando, certifica-te que o locale do admin user está definido para `pt_PT` em **Admin > Settings > Users** para veres os nomes na listagem de produtos.

## Licença

MIT
