<?php if ($this->Paginator->counter('{:pages}') > 1) { ?>
    <div class="paging_full_numbers" id="example_paginate">
        <?php
        echo $this->Paginator->first('First');
        // Shows the next and previous links
        echo $this->Paginator->prev('Previous', null, null, array('class' => 'disabled'));
        // Shows the page numbers
        echo $this->Paginator->numbers(array('separator' => ''));
        echo $this->Paginator->next('Next', null, null, array('class' => 'disabled'));
        // prints X of Y, where X is current page and Y is number of pages
        //echo $this->Paginator->counter();
        echo $this->Paginator->last('Last');
        ?>
    </div>
    <?php echo $this->Html->css('pagination'); ?>
<?php } ?>