<?php
$f = $edit_movie ?? [];
?>

<div class="form-group">
    <label>Movie Title <span class="req">*</span></label>
    <input type="text" name="title" required
           value="<?= htmlspecialchars($f['title'] ?? '') ?>"
           placeholder="e.g. Inception">
</div>

<div class="form-row">
    <div class="form-group">
        <label>Category <span class="req">*</span></label>
        <select name="category" required>
            <option value="Hollywood" <?= ($f['category'] ?? '') === 'Hollywood' ? 'selected' : '' ?>>🎬 Hollywood</option>
            <option value="Anime"     <?= ($f['category'] ?? '') === 'Anime'     ? 'selected' : '' ?>>⛩️ Anime</option>
        </select>
    </div>
    <div class="form-group">
        <label>Rating (0 – 10) <span class="req">*</span></label>
        <input type="number" name="rating" step="0.1" min="0" max="10" required
               value="<?= htmlspecialchars($f['rating'] ?? '') ?>"
               placeholder="8.5">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label>Genre <span class="req">*</span></label>
        <input type="text" name="genre" required
               value="<?= htmlspecialchars($f['genre'] ?? '') ?>"
               placeholder="Action, Sci-Fi">
    </div>
    <div class="form-group">
        <label>Release Year <span class="req">*</span></label>
        <input type="number" name="year" min="1900" max="2099" required
               value="<?= htmlspecialchars($f['year'] ?? date('Y')) ?>"
               placeholder="2024">
    </div>
</div>

<div class="form-group">
    <label>Poster Image URL</label>
    <input type="url" name="poster_url"
           value="<?= htmlspecialchars($f['poster_url'] ?? '') ?>"
           placeholder="https://...">
</div>

<div class="form-group">
    <label>Description <span class="req">*</span></label>
    <textarea name="description" rows="4" required
              placeholder="Write a short summary of the movie..."><?= htmlspecialchars($f['description'] ?? '') ?></textarea>
</div>
