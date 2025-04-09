<div wire:loading.flex wire:target="*" id='idsContenedor' class="lds-contenedor">
    <div class="lds-ring">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
    </div>
</div>
@if(Session::has('error'))
    <div id='errorModal' class="mi-modal-error" style="display:flex">
        <div class="mi-modal-content">
            <div id='modalHeader' class="mi-modal-header-error">
                <span class="mi-modal-close" onclick="errorModal.style.display='none';">&times;</span>
                <h2 id='modalTitle'>Aviso</h2>
            </div>
            <div class="mi-modal-body">
                <ul id="listaErroresModal" class="p-0 m-0" style="list-style: none;">
                    <li>{{Session::get('error')}}</li>
                </ul>
            </div>
            <div id='modalFooter' class="mi-modal-footer-error">
                <h3></h3>
            </div>
        </div>
    </div>
@endif
