<?php
/**
* @var array $categories Массив категорий
* @var array $lot лот
* @var array $bets Массив ставок
*/
?>
   <nav class="nav">
   <ul class="nav__list container">
     <?php foreach ($categories as $category): ?>
        <li class="nav__item">
        <a href="all-lots.html"><?=$category['name']; ?></a>
        </li>
    <?php endforeach; ?>
   </ul>
 </nav>
 <section class="lot-item container">
   <h2><?=$lot['title'] ?></h2>
   <div class="lot-item__content">
     <div class="lot-item__left">
       <div class="lot-item__image">
         <img src="<?=$lot['img_url'] ?>" width="730" height="548" alt="Сноуборд">
       </div>
       <p class="lot-item__category">Категория: <span><?=$lot['category']; ?></span></p>
       <p class="lot-item__description"><?=$lot['description']; ?></p>
     </div>
     <div class="lot-item__right">
       <div class="lot-item__state">
         <div class="lot-item__timer timer">
            <?=get_time_left();  ?>
         </div>
         <div class="lot-item__cost-state">
           <div class="lot-item__rate">
             <span class="lot-item__amount">Текущая цена</span>
             <span class="lot-item__cost"><?=$bets[0]['price'] ?? $lot['start_price']; ?></span>
           </div>
           <div class="lot-item__min-cost">
             Мин. ставка <span><?=($bets[0]['price'] ?? $lot['start_price']) + $lot['bet_step'];  ?></span>
           </div>
         </div>
         <form class="lot-item__form" action="https://echo.htmlacademy.ru" method="post">
           <p class="lot-item__form-item form__item form__item--invalid">
             <label for="cost">Ваша ставка</label>
             <input id="cost" type="text" name="cost" placeholder="<?=($bets[0]['price'] ?? $lot['start_price']) + $lot['bet_step'];?>">
             <span class="form__error">Введите наименование лота</span>
           </p>
           <button type="submit" class="button">Сделать ставку</button>
         </form>
       </div>
       <div class="history">
         <h3>История ставок (<span><?=count($bets); ?></span>)</h3>
         <table class="history__list">
            <?php foreach($bets as $bet): ?> 
              <tr class="history__item">
                <td class="history__name"><?=$bet['user_name']; ?></td>
                <td class="history__price"><?=$bet['price']; ?></td>
                <td class="history__time"><?=$bet['date_create']; ?></td>
              </tr>
            <?php endforeach; ?>
         </table>
       </div>
     </div>
   </div>
 </section>