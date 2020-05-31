Scopul acestui proiect a fost de a "reproduce" paginile de google pe mobile. Tabele au fost populate in felul urmator:
- results: sunt preluate cateva site-uri din care luat articolele de pe prima pagina
- suggestions: sunt preluate cuvintele limbii romane de pe dex.ro

1. git clone https://github.com/CostiNec/ControlF5App
2. composer install
3. creati un fisier .env si configurati-l conform .env.example
4. php artisan migrate:fresh
5. php artisan db:seed (aceasta va dura cca 4 minute, deoarece sunt preluate toate cuvintele limbii romane si sunt bagate in tabela de suggestions)
6. php artisan key:generate
7. php artisan serve
8. intrati in link si mergeti pe ruta /seed (ex: localhost:8000/seed) si asteptati pana apare alerta in care va spune ca articolele au fost inserate in baza de date (acestea sunt bagate in tabela de results)


