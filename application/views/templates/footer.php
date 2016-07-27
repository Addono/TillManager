
            </div><!-- main -->
            <?php // Show the log, if there is anything to show.
                if(isset($log)) {
                    if($log != "" && $log != "&nbsp;") {
                        echo $log;
                    }
                }
            ?>
        <em>Adriaan Knapen &copy; 2016 (Beta, WIP)</em> <!-- Koen was here! -->
        </div><!-- page -->

        <?php // If a menu is required, initiate jQuery mobile to build it for us.
        if($navigation) { ?>
        <script>
            // Binds swipe functionality to the header.
            function make_header_swipeable(page) {
                // Set the thresholds for the swipe gestures.
                $.event.special.swipe.scrollSupressionThreshold = (screen.availWidth) / 50;
                $.event.special.swipe.horizontalDistanceThreshold = (screen.availWidth) / 50;
                $.event.special.swipe.verticalDistanceThreshold = (screen.availHeight) / 30;

                // Add the
                $("#header", page).on("swiperight", function(event) {
                   $("#nav-button").click();
                });
            }

            // Update the layout of the navigation panel.
            $("#nav-panel").trigger("updatelayout");

            // Bind swipe functionality to the header on initial page load.
            $(document).on("pagecontainershow", function ( event, ui ) {
                make_header_swipeable(ui.page);
            });

            // Bind swipe functionality to the header after page transition.
            $(document).on("pagecontainerload", function ( event, ui) {
                make_header_swipeable(ui.page);
            });
        </script>
        <?php } ?>
    </body>
</html>
