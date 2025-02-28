var SideBar;

SideBar = (function($, window) {
  "use strict";

  var config, expadSidebar, handleSideBarAccordions, handleSideBarScroll, handleSideBarSubMenus, handleSideBarWidth, handleUserSettingsContainer, init, isRTL, isTouchDevice, reduceSidebar, sidebarScroll, socialBarContainer, userSettingsContainer;
  config = {
    shortenOnClickOutside: true,
    sidebarContainer: $(".social-sidebar"),
    userSettingsContainer: $(".social-sidebar .user-settings"),
    sidebarScrollContainer: $(".social-sidebar-content .scrollable")

  };
  /**/

  socialBarContainer = config.sidebarContainer;
  userSettingsContainer = config.userSettingsContainer;
  sidebarScroll = config.sidebarScrollContainer;

  /* Check for device touch support
     Based on https://github.com/Modernizr/Modernizr/blob/master/feature-detects/touchevents.js
  */

  isTouchDevice = function() {
    if (("ontouchstart" in window) || window.DocumentTouch && document instanceof DocumentTouch) {
      return true;
    } else {
      return false;
    }
  };
  /**/


  /**/

  isRTL = function() {
    return $("body").hasClass('rtl');
  };
  /**/

  init = function(options) {
    $.extend(config, options);
    handleUserSettingsContainer();
    handleSideBarWidth();
    handleSideBarAccordions();
    handleSideBarSubMenus();
    handleSideBarScroll();

  };
  /**/

  handleUserSettingsContainer = function() {
    /* Hide user-settings menu when click on its content */
    userSettingsContainer.find(".user-settings-content").on("click", function(e) {
      e.stopPropagation();
      userSettingsContainer.toggle();
    });
    /* Hide user-settings menu when click on its link in the footer area*/

    userSettingsContainer.find(".user-settings-footer a").on("click", function(e) {
      e.stopPropagation();
      userSettingsContainer.toggle();
    });
    /* Handle when click outside the user settings menu*/

    /*userSettingsContainer.clickOutside(function(event, obj) {
      /!* if it's clicked the user settings trigger (small upper left icon)
         the user settings menu will be hidden or shown **!/
      if (event.target.className.indexOf("trigger-user-settings") >= 0) {
        obj.toggle();
      /!* If we dom't click on the trigger or the user settings menu, this will
        be hide *!/
      } else {
        obj.hide();
      }
    });*/
  };
  /* Expand the width of the sidebar*/

  expadSidebar = function() {
    var marginLeft, marginRight;
    $("body").css("overflow-x", "hidden");

    /**/


    socialBarContainer.addClass("sidebar-full");
    $(".wraper").addClass("sidebar-full").removeClass("sidebar-icon");
    $(".social-sidebar").addClass("sidebar-full");
    //$(".user-settings").hide();
    if (isRTL()) {
      $("#main").css("margin-left", marginLeft).css("margin-right", 200);
      $("#main_content").css("margin-left", marginLeft).css("margin-right", 200);
      $(".social-navbar").css("right", 200);
    } else {
      $("#main").css("margin-right", marginRight).css("margin-left", 200);
      $("#main_content").css("margin-right", marginRight).css("margin-left", 200);
      $(".social-navbar").css("left", 200);
    }
  };
  /* Reduce the width of the sidebar*/

  reduceSidebar = function() {
    /**/

    var marginRight;
    userSettingsContainer.hide();

    socialBarContainer.find(".accordion-body.in").collapse("hide");


    $(".wraper").removeClass("sidebar-full");
    $(".social-sidebar").removeClass("sidebar-full");
    socialBarContainer.removeClass("sidebar-full");
    if (isRTL()) {
      $("#main").css("margin-left", 0).css("margin-right", 54);
      $("#main_content").css("margin-left", 0).css("margin-right", 54);
      $(".social-navbar").css("right", 54);
    } else {
      $("#main").css("margin-right", 0).css("margin-left", 54);
      $("#main_content").css("margin-right", 0).css("margin-left", 54);
      $(".social-navbar").css("left", 54);
    }
  };
  /**/

  handleSideBarWidth = function() {
    /* === Hide/Show Sidebar ===*/

    $(".switch-sidebar-icon").click(function() {

      /*if (socialBarContainer.hasClass("sidebar-full")) {
        reduceSidebar();
      } else {
        expadSidebar();
      }*/
      if ($('.wraper').hasClass("sidebar-full") && $('.social-sidebar').hasClass("sidebar-full")) {
        reduceSidebar();
      } else {
        expadSidebar();
      }


      return false;
    });
    $(".search-sidebar img, .search-sidebar i, .user-filter .dropdown-toggle").click(function() {
      expadSidebar();
      return false;
    });
    if (isTouchDevice === true) {
      sidebarScroll.find(".navigation-sidebar").show();
    } else {
      socialBarContainer.on("mouseleave", function(event) {

        return false;
      });
      socialBarContainer.on("mouseenter", function(event) {

        return false;
      });
    }
    if (config.shortenOnClickOutside === true) {
      socialBarContainer.clickOutside(function(event, obj) {
        if ($(window).width() >= 979) {
          reduceSidebar();
        }
      });
    }
    socialBarContainer.on("show", function() {
      expadSidebar();
    });
    $(window).resize(function() {
      if ($(window).width() < 979) {
        expadSidebar();
      }
    });
  };
  /**/

  handleSideBarAccordions = function() {
    /* Action when a menu of the sidebar will be shown*/

    $(".accordion-body").on("show", function() {
      $(this).parent().find(".accordion-toggle").addClass("opened");
      if (!$(".wraper").hasClass("sidebar-full")) {
        $(".social-sidebar").addClass("sidebar-full");
        $(".wraper").addClass("sidebar-full");
        $(".wraper").addClass("sidebar-icon");
      }
    });
    /* Action when a menu of the sidebar will be hidden*/

    $(".accordion-body").on("hide", function() {
      $(this).parent().find(".accordion-toggle").removeClass("opened");
      if ($(".wraper").hasClass("sidebar-icon")) {
        $(".social-sidebar").removeClass("sidebar-full");
        $(".wraper").removeClass("sidebar-full");
      }
    });
  };
  /* Sidebar Scroll */

  handleSideBarSubMenus = function() {
    $("[data-toggle='sub-menu-collapse']").click(function() {
      var target;
      target = $($(this).attr("data-target"));
      if (target.hasClass("in")) {
        $(this).removeClass("opened");
        target.removeClass("in");
      } else {
        $(this).addClass("opened");
        target.addClass("in");
      }
    });
  };
  /**/

  handleSideBarScroll = function() {
    var fixeSidebarScroll, resizeHandler, sidebarScrollOptions;

    /* Init Options for the scroll of the sidebar*/
    sidebarScrollOptions = {
          size: "8px",
          railVisible: true,
          railColor: "#000"
        };
        if (isRTL()) {
          sidebarScrollOptions.position = 'left';
        }
    /* This function handle the sidebar scrroll whe the window is resized*/

    resizeHandler = function() {
        var windowHeight;
        windowHeight = $(window).height();
        //sidebarScroll.css("height", (windowHeight - chatUsersContainer.outerHeight()) + "px");
        sidebarScroll.css("height", (windowHeight) + "px");
        //$(".social-sidebar-content").find(".slimScrollDiv").css("height", (windowHeight - chatUsersContainer.outerHeight()) + "px");
        $(".social-sidebar-content").find(".slimScrollDiv").css("height", (windowHeight) + "px");
      };
      /* This will reset the sidebar scrollbar*/

      fixeSidebarScroll = function() {
        if ($(window).width() <= 979) {
          $(".social-sidebar .slimScrollDiv").attr('style', '');
          if (socialBarContainer.hasClass("in")) {
            sidebarScroll.css("height", $(window).height() - $(".social-navbar").height());
            $(".social-sidebar-content").css("height", $(document).height());
          }
        }
      };
    /* Init the sidebar scroll*/

    sidebarScroll.slimscroll(sidebarScrollOptions);
    /* Init the the sidebar scroll handler when the window is risized*/

    $(window).resize(function() {
      if ($(window).width() > 979) {
        resizeHandler();
        sidebarScroll.attr("style", "");
      }
      fixeSidebarScroll();
    });
    socialBarContainer.on("show", function() {
      sidebarScroll.css("height", $(window).height() - $(".social-navbar").height());
      fixeSidebarScroll();
    });
    /* This update the scroll when an element of the side is shown*/

    $(".social-sidebar .accordion-body").on("shown", function() {

      sidebarScroll.slimscroll(sidebarScrollOptions);
    });
    /* This update the scroll when an element of the side is hidenof the side*/

    $(".social-sidebar .accordion-body").on("hidden", function() {

      sidebarScroll.slimscroll(sidebarScrollOptions);
    });
    /* We make sure that the user settings menu will be hiden when we scroll
     the sidebar
    */

    sidebarScroll.bind("slimscrolling", function(e, pos) {
      userSettingsContainer.hide();
    });
  };
  /**/



  return {
    init: init,
    isTouchDevice: isTouchDevice
  };
})(jQuery, window);