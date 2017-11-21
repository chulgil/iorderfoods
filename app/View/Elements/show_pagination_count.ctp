<div class="row">
    <div class="col-sm-6">
        <?php echo $this->Paginator->counter('Page {:page} of {:pages}'); ?> 
    </div>
    <div class="col-sm-6 text-right">
        <?php echo $this->Paginator->counter('showing {:current} records out of {:count} total'); ?> 
    </div>
</div>