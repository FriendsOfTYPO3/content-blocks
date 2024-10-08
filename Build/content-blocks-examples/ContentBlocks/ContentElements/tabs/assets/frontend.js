function openTab(ev) {
  let tabTarget = ev.currentTarget.getAttribute('data-target');
  let tabContent = document.getElementsByClassName("tab-content");
  for (let i = 0; i < tabContent.length; i++) {
    tabContent[i].style.display = "none";
  }
  let tabLinks = document.getElementsByClassName("tab-links");
  for (let i = 0; i < tabLinks.length; i++) {
    tabLinks[i].className = tabLinks[i].className.replace(" active", "");
  }
  document.getElementById(tabTarget).style.display = "block";
  ev.currentTarget.className += " active";
}

document.querySelectorAll('.cb-tabs .tab-links').forEach(function (item) {
  item.addEventListener('click', function (event) {
    openTab(event);
  });
});
