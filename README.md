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

## Licença

MIT
