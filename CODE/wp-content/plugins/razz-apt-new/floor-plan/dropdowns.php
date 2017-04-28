<div id="menu-dd" class="dropdown dropdown-tip">
    <ul class="dropdown-menu">
        <li><a href="#" class="fp-modal-close hidemobile">Floors</a></li>
        <li><a href="#" class="fp-modal-close showmobile">Quick Search</a></li>
        <li><?php reset($models); $tm = $models[key($models)]['name']; ?><a href="model.php?id=<?= urlencode($tm) ?>" class="goto-model" data-model="<?= htmlentities($tm) ?>">Models</a></li>
    </ul>
</div>
<div id="model-dd" class="dropdown dropdown-tip">
    <ul class="dropdown-menu">
        <?php foreach($models as $m): ?>
            <li><a href="model.php?id=<?= urlencode($m['name']) ?>" class="goto-model" data-model="<?= htmlentities($m['name']) ?>">Model <?= htmlentities($m['name']) ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>
<div id="unit-dd" class="dropdown dropdown-tip">
    <ul class="dropdown-menu">
        <?php foreach($units as $unit): ?>
            <li><a href="unit.php?id=<?= urlencode($unit['id']) ?>" class="goto-unit" data-unit="<?= htmlentities($unit['id']) ?>">#<?= htmlentities($unit['id']) ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>
<div id="floor-dd" class="dropdown dropdown-tip">
    <ul class="dropdown-menu">
        <?php foreach($floors as $floor): if(!$floor['verticies']) continue; ?>
            <li><a href="floor.php?id=<?= urlencode($floor['id']) ?>" class="goto-floor" data-floor="<?= $floor['id']; ?>">Floor <?= $floor['id']; ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>