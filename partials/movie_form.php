<?php
$f = $edit_movie ?? [];
?>

<div class="form-group">
    <label>Movie Title <span class="req">*</span></label>
    <input type="text" name="title" required
           value="<?= htmlspecialchars($f['title'] ?? '') ?>"
           placeholder="e.g. Inception (2010)">
</div>

<div class="form-row">
    <div class="form-group">
        <label>Primary Genre <span class="req">*</span></label>
        <select name="genre" required>
            <?php 
            $genres = ['Action', 'Adventure', 'Biography', 'Comedy', 'Crime', 'Drama', 'Family', 'Fantasy', 'Mystery', 'Sci-Fi'];
            foreach ($genres as $g): 
            ?>
            <option value="<?= $g ?>" <?= ($f['genre'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Director</label>
        <input type="text" name="director"
               value="<?= htmlspecialchars($f['director'] ?? '') ?>"
               placeholder="e.g. Christopher Nolan">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label>Rating (0.0 – 10.0) <span class="req">*</span></label>
        <input type="number" name="rating" step="0.1" min="0" max="10" required
               value="<?= htmlspecialchars($f['rating'] ?? '') ?>"
               placeholder="8.8">
    </div>
    <div class="form-group">
        <label>Release Year <span class="req">*</span></label>
        <input type="number" name="year" min="1900" max="2099" required
               value="<?= htmlspecialchars($f['year'] ?? date('Y')) ?>"
               placeholder="2010">
    </div>
</div>

<div class="form-group">
    <label>Poster Image URL</label>
    <input type="url" name="poster_url"
           value="<?= htmlspecialchars($f['poster_url'] ?? '') ?>"
           placeholder="https://...">
</div>

<div class="form-group">
    <label>Movie Description / Synopsis <span class="req">*</span></label>
    <textarea name="description" rows="4" required
              placeholder="Enter synopsis..."><?= htmlspecialchars($f['description'] ?? '') ?></textarea>
</div>
