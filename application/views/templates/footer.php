            </div><!-- main -->
        <?php $this->Util->resources->add(['jquery', 'jquery-mobile']); ?>
        <em>Made by Adriaan Knapen - WIP &copy; 2016</em>
        
        </div><!-- page -->
        
        <?php // If a menu is required, initiate jQuery mobile to build it for us.
        if($navigation) { ?>
        <script>
            // Set the thresholds for the swipe gestures.
            $.event.special.swipe.scrollSupressionThreshold = (screen.availWidth) / 9;
            $.event.special.swipe.horizontalDistanceThreshold = (screen.availWidth) / 9;
            $.event.special.swipe.verticalDistanceThreshold = (screen.availHeight) / 9;
            
            // Update the layout of the navigation panel.
            $("#nav-panel").trigger("updatelayout");
            
            // Allow opening of the navigation panel by swiping.
            $("#header").on("swiperight", function(event) {
               $("#nav-button").click();
            });
        </script>
        <?php } ?>
    </body>
</html>