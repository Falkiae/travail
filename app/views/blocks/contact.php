<?php
require_once __DIR__ . '/_block_helpers.php';

static $knContactCount = 0;
$knContactCount++;
$uid      = 'kn-contact-' . $knContactCount;
$anchorId = $knContactCount === 1 ? 'contact' : $uid;

$bg     = isset($block['bg']) ? $block['bg'] : 'cream';
$isDark = ($bg === 'blue' || $bg === 'night');

$ch = isset($contact_channels) && is_array($contact_channels) ? $contact_channels : array();
$on = function ($key) use ($block) { return !isset($block[$key]) || !empty($block[$key]); };

$t = function ($key, $default) use ($block) {
    return isset($block[$key]) && trim((string) $block[$key]) !== '' ? (string) $block[$key] : $default;
};
$eyebrow    = $t('eyebrow', 'UNE QUESTION OU UN PROJET ?');
$h2         = $t('h2', 'Parlons de <span class="hl">votre besoin</span>');
$intro      = $t('intro', 'Le formulaire de réservation ne correspond pas tout à fait à votre situation ? Devis sur mesure, question, cas particulier : écrivez-nous, nous vous répondons personnellement.');
$formTitle  = $t('form_title', 'Écrivez-nous');
$formIntro  = $t('form_intro', 'Quelques mots suffisent, nous revenons vers vous rapidement.');
$submit     = $t('submit_label', 'Envoyer mon message');
$note       = $t('note', 'Réponse personnelle sous 24 h ouvrées. Vos données ne servent qu\'à répondre à votre demande.');
$okTitle    = $t('success_title', 'Message bien reçu !');
$okText     = $t('success_text', 'Merci, nous vous répondons personnellement sous 24 h ouvrées. Pour une demande urgente, appelez-nous.');
$consent    = $t('consent_text', 'J\'accepte que mes données soient utilisées pour répondre à ma demande.');
$privLabel  = $t('privacy_label', 'Politique de confidentialité');
$privUrl    = $t('privacy_url', isset($ch['privacy_url']) ? (string) $ch['privacy_url'] : '');
$chanTitle  = $t('channels_title', 'Ou contactez-nous directement');
$waText     = $t('whatsapp_text', 'Bonjour, j\'aimerais avoir des informations / un devis sur mesure.');

$reassure = isset($block['items']) && is_array($block['items']) ? $block['items'] : array(
    array('icon' => 'clock',        'text' => 'Réponse sous 24 h ouvrées'),
    array('icon' => 'shield-check', 'text' => 'Devis gratuit et sans engagement'),
    array('icon' => 'lock',         'text' => 'Vos données restent confidentielles'),
);
$subjectsRaw = $t('subjects', "Devis sur mesure\nUne question\nAutre");
$subjects    = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $subjectsRaw)), 'strlen'));
if (!$subjects) $subjects = array('Devis sur mesure', 'Une question', 'Autre');

// Canaux de contact direct (n'apparaissent que s'ils sont renseignés dans les réglages et activés dans le bloc)
$channels = array();
if ($on('show_whatsapp') && !empty($ch['whatsapp'])) {
    $channels[] = array('cls' => 'whatsapp', 'icon' => 'whatsapp', 'label' => 'WhatsApp', 'href' => 'https://wa.me/' . $ch['whatsapp'] . '?text=' . rawurlencode($waText), 'ext' => true);
}
if ($on('show_messenger') && !empty($ch['messenger'])) {
    $channels[] = array('cls' => 'messenger', 'icon' => 'messenger', 'label' => 'Messenger', 'href' => $ch['messenger'], 'ext' => true);
}
if ($on('show_phone') && !empty($ch['phone'])) {
    $channels[] = array('cls' => 'phone', 'icon' => 'phone', 'label' => $ch['phone'], 'href' => 'tel:' . preg_replace('/[^+0-9]/', '', $ch['phone']), 'ext' => false);
}
if ($on('show_email') && !empty($ch['email'])) {
    $channels[] = array('cls' => 'mail', 'icon' => 'mail', 'label' => $ch['email'], 'href' => 'mailto:' . $ch['email'], 'ext' => false);
}
$socials = ($on('show_socials') && !empty($ch['socials']) && is_array($ch['socials'])) ? $ch['socials'] : array();
$socialLabels = array('facebook' => 'Facebook', 'instagram' => 'Instagram', 'linkedin' => 'LinkedIn', 'twitter' => 'X (Twitter)');

$flag    = isset($_GET['contact']) ? (string) $_GET['contact'] : '';
$showOk  = $flag === 'ok';
$showErr = $flag === 'err';

$stamp   = \App\Controllers\ContactController::stamp();
$token   = \App\Controllers\ContactController::token();
$pageUrl = isset($_SERVER['REQUEST_URI']) ? (string) parse_url((string) $_SERVER['REQUEST_URI'], PHP_URL_PATH) : '';
?>
<section class="<?php echo blockClasses($block, 'cream', 'kn-contact'); ?>" id="<?php echo $anchorId; ?>">
  <div class="container">
    <div class="kn-contact__grid">

      <div class="kn-contact__info">
        <span class="kn-eyebrow"><?php echo htmlspecialchars($eyebrow); ?></span>
        <h2 class="kn-section__title"><?php echo $h2; ?></h2>
        <p class="kn-contact__intro"><?php echo htmlspecialchars($intro); ?></p>

        <?php if ($reassure): ?>
        <ul class="kn-contact__reassure">
          <?php foreach ($reassure as $r):
            $txt = is_array($r) ? (isset($r['text']) ? (string) $r['text'] : '') : (string) $r;
            if (trim($txt) === '') continue;
            $ico = is_array($r) && !empty($r['icon']) ? $r['icon'] : 'check';
          ?>
          <li><span class="kn-contact__reassure-ico" aria-hidden="true"><?php echo knIcon($ico, array('size' => 18, 'fallback' => 'check')); ?></span><span><?php echo htmlspecialchars($txt); ?></span></li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>

        <?php if ($channels): ?>
        <div class="kn-contact__channels">
          <p class="kn-contact__channels-title"><?php echo htmlspecialchars($chanTitle); ?></p>
          <div class="kn-contact__chan-list">
            <?php foreach ($channels as $c): ?>
            <a class="kn-contact__chan kn-contact__chan--<?php echo $c['cls']; ?>" href="<?php echo htmlspecialchars($c['href']); ?>"<?php echo $c['ext'] ? ' target="_blank" rel="noopener"' : ''; ?>>
              <?php echo knIcon($c['icon'], array('size' => 20)); ?><span><?php echo htmlspecialchars($c['label']); ?></span>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <?php if ($socials): ?>
        <div class="kn-contact__socials" aria-label="Nos réseaux sociaux">
          <?php foreach ($socials as $net => $url): if (!isset($socialLabels[$net])) continue; ?>
          <a href="<?php echo htmlspecialchars($url); ?>" target="_blank" rel="noopener" aria-label="<?php echo htmlspecialchars($socialLabels[$net]); ?>" title="<?php echo htmlspecialchars($socialLabels[$net]); ?>"><?php echo knIcon($net, array('size' => 20)); ?></a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <div class="kn-contact__card">
        <div class="kn-contact__success" tabindex="-1" role="status"<?php echo $showOk ? '' : ' hidden'; ?>>
          <span class="kn-contact__success-ico" aria-hidden="true"><?php echo knIcon('check', array('size' => 28)); ?></span>
          <h3><?php echo htmlspecialchars($okTitle); ?></h3>
          <p><?php echo htmlspecialchars($okText); ?></p>
        </div>

        <form class="kn-contact__form" method="post" action="/contact/send" novalidate data-contact-form<?php echo $showOk ? ' hidden' : ''; ?>>
          <h3 class="kn-contact__form-title"><?php echo htmlspecialchars($formTitle); ?></h3>
          <?php if ($formIntro !== ''): ?><p class="kn-contact__form-intro"><?php echo htmlspecialchars($formIntro); ?></p><?php endif; ?>

          <input type="hidden" name="_token" value="<?php echo htmlspecialchars($token); ?>">
          <input type="hidden" name="ts" value="<?php echo htmlspecialchars($stamp['ts']); ?>">
          <input type="hidden" name="sig" value="<?php echo htmlspecialchars($stamp['sig']); ?>">
          <input type="hidden" name="page" value="<?php echo htmlspecialchars($pageUrl); ?>">
          <div class="kn-contact__trap" aria-hidden="true"><label>Ne pas remplir<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

          <div class="kn-contact__row">
            <div class="kn-contact__field">
              <label for="<?php echo $uid; ?>-name">Votre nom</label>
              <input type="text" id="<?php echo $uid; ?>-name" name="name" autocomplete="name" required maxlength="100">
              <p class="kn-contact__error" data-error-for="name"></p>
            </div>
            <div class="kn-contact__field">
              <label for="<?php echo $uid; ?>-email">Votre e-mail</label>
              <input type="email" id="<?php echo $uid; ?>-email" name="email" autocomplete="email" inputmode="email" required maxlength="190">
              <p class="kn-contact__error" data-error-for="email"></p>
            </div>
          </div>

          <div class="kn-contact__row">
            <div class="kn-contact__field">
              <label for="<?php echo $uid; ?>-phone">Téléphone <small>(facultatif)</small></label>
              <input type="tel" id="<?php echo $uid; ?>-phone" name="phone" autocomplete="tel" inputmode="tel" maxlength="30">
              <p class="kn-contact__error" data-error-for="phone"></p>
            </div>
            <div class="kn-contact__field">
              <label for="<?php echo $uid; ?>-subject">Votre demande</label>
              <select id="<?php echo $uid; ?>-subject" name="subject">
                <?php foreach ($subjects as $s): ?><option value="<?php echo htmlspecialchars($s); ?>"><?php echo htmlspecialchars($s); ?></option><?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="kn-contact__field">
            <label for="<?php echo $uid; ?>-message">Votre message</label>
            <textarea id="<?php echo $uid; ?>-message" name="message" rows="5" required minlength="10" maxlength="3000" placeholder="Décrivez votre besoin : type de véhicule ou de mobilier, lieu, délais…"></textarea>
            <p class="kn-contact__error" data-error-for="message"></p>
          </div>

          <div class="kn-contact__field kn-contact__field--check">
            <label class="kn-contact__consent">
              <input type="checkbox" name="consent" value="1" required>
              <span><?php echo htmlspecialchars($consent); ?><?php if ($privUrl !== ''): ?> <a href="<?php echo htmlspecialchars($privUrl); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($privLabel); ?></a><?php endif; ?></span>
            </label>
            <p class="kn-contact__error" data-error-for="consent"></p>
          </div>

          <p class="kn-contact__status" role="alert" aria-live="polite"<?php echo $showErr ? '' : ' hidden'; ?>><?php echo $showErr ? 'Merci de vérifier les champs puis de réessayer.' : ''; ?></p>
          <button type="submit" class="kn-btn kn-contact__submit"><?php echo knIcon('send', array('size' => 16)); ?><span><?php echo htmlspecialchars($submit); ?></span></button>
          <?php if ($note !== ''): ?><p class="kn-contact__note"><?php echo knIcon('lock', array('size' => 14)); ?><span><?php echo htmlspecialchars($note); ?></span></p><?php endif; ?>
        </form>
      </div>

    </div>
  </div>
</section>
<script>
(function () {
  var root = document.getElementById('<?php echo $anchorId; ?>');
  if (!root || root.dataset.init) return;
  root.dataset.init = '1';
  var form = root.querySelector('[data-contact-form]');
  var okBox = root.querySelector('.kn-contact__success');
  var status = root.querySelector('.kn-contact__status');
  var btn = root.querySelector('.kn-contact__submit');
  if (!form) return;

  function clearErrors() {
    form.querySelectorAll('[data-error-for]').forEach(function (p) { p.textContent = ''; });
    form.querySelectorAll('[aria-invalid]').forEach(function (i) { i.removeAttribute('aria-invalid'); });
    status.hidden = true; status.textContent = '';
  }
  function setError(name, msg) {
    var p = form.querySelector('[data-error-for="' + name + '"]');
    if (p) p.textContent = msg;
    var f = form.elements[name];
    if (f) f.setAttribute('aria-invalid', 'true');
  }
  function showStatus(msg) { status.textContent = msg; status.hidden = false; }
  function clientValidate() {
    var bad = null, v = function (n, m) { if (!bad) bad = form.elements[n]; setError(n, m); };
    var n = form.elements.name.value.trim(), e = form.elements.email.value.trim(), m = form.elements.message.value.trim();
    if (n.length < 2) v('name', 'Indiquez votre nom.');
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(e)) v('email', 'Indiquez une adresse e-mail valide.');
    if (m.length < 10) v('message', 'Décrivez votre demande en quelques mots (10 caractères minimum).');
    if (!form.elements.consent.checked) v('consent', 'Merci de cocher la case pour que nous puissions vous répondre.');
    if (bad) { bad.focus(); return false; }
    return true;
  }

  form.addEventListener('submit', function (ev) {
    ev.preventDefault();
    clearErrors();
    if (!clientValidate()) return;
    btn.disabled = true; btn.classList.add('is-loading');
    fetch(form.action, {
      method: 'POST',
      body: new FormData(form),
      credentials: 'same-origin',
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    }).then(function (r) {
      return r.json().then(function (j) { return { status: r.status, json: j }; }, function () { return { status: r.status, json: {} }; });
    }).then(function (res) {
      if (res.json && res.json.ok) {
        form.hidden = true; okBox.hidden = false; okBox.focus();
        okBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        form.reset();
        return;
      }
      var errs = (res.json && res.json.errors) || {};
      Object.keys(errs).forEach(function (k) { setError(k, errs[k]); });
      var first = Object.keys(errs)[0];
      if (first && form.elements[first]) form.elements[first].focus();
      showStatus((res.json && res.json.error) || 'Une erreur est survenue. Réessayez ou contactez-nous directement.');
    }).catch(function () {
      showStatus('Connexion impossible pour le moment. Réessayez ou contactez-nous directement.');
    }).then(function () { btn.disabled = false; btn.classList.remove('is-loading'); });
  });
})();
</script>
