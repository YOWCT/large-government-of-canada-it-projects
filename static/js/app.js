// Page JS for the Large Government of Canada IT projects website
// Created 2020-04-19

var app = app || {};

$(function () {
  app.modalContentsTemplate = _.template($("#modalContentsTemplate").html());

  app.hashChanged = function (anchor) {
    var targetId;
    var item;

    if (_.startsWith(anchor, "#uid=") === true) {
      targetId = anchor.substring(5);
      item = _.get(app.data, targetId);
      // console.log("targetId = " + targetId);

      if (_.isObject(item)) {
        // console.log(app.modalContentsTemplate(item));
        $("#modal-details-body").html(
          app.modalContentsTemplate({ item: item })
        );
        $("#modal-details").modal("show");
        return true;
      }
    }
    // console.log("No item selected");
  };

  // Thanks to
  // https://stackoverflow.com/a/2162174
  if ("onhashchange" in window) {
    // event supported?
    window.onhashchange = function () {
      app.hashChanged(window.location.hash);
    };
  } else {
    // event not supported:
    var storedHash = window.location.hash;
    window.setInterval(function () {
      if (window.location.hash != storedHash) {
        storedHash = window.location.hash;
        app.hashChanged(storedHash);
      }
    }, 100);
  }

  // Modal "hide" event should clear any existing anchor
  $("#modal-details").on("hidden.bs.modal", function (e) {
    // Reset anchor
    window.location.hash = "uid=";
  });
});

$(document).ready(function () {
  // Initialize the data table
  var orderColumnIndex = _.toInteger($("#main-data-table").data("orderColumn"));
  $("#main-data-table").DataTable({
    order: [[orderColumnIndex, "desc"]],
    lengthMenu: [
      [20, 50, 100, -1],
      [20, 50, 100, "All"],
    ],
  });

  // Scroll to top of table on change
  // Thanks to
  // https://stackoverflow.com/a/35899744
  $("#main-data-table").on("page.dt", function () {
    $("html, body").animate(
      {
        scrollTop: $("#main-data-table").offset().top,
      },
      200
    );
  });

  // Check for any anchor onload
  app.hashChanged(window.location.hash);

  // Add target=_blank to external links
  // Thanks to http://css-tricks.com/snippets/jquery/open-external-links-in-new-window/
  $("#main a[href^='http://']").attr("target", "_blank");
  $("#main a[href^='https://']").attr("target", "_blank");
});
