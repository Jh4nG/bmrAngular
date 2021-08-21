import { Component, OnInit } from '@angular/core';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import { Cliente } from 'src/app/models/cliente';
import { Pedido } from 'src/app/models/pedido';
import { Producto } from 'src/app/models/producto';
import { BrmServiceService } from 'src/app/services/brm-service.service';

@Component({
  selector: 'app-form-compra',
  templateUrl: './form-compra.component.html',
  styleUrls: ['./form-compra.component.css']
})
export class FormCompraComponent implements OnInit {

  clientesSelect = "";
  productoSelect = "";
  cantidadSelect = "";
  precio = 0;
  clientes : Cliente[] = [];
  productos : Producto[] = [];
  formCompra = new FormGroup({
    clienteCtrl : new FormControl('', Validators.required),
    productoCtrl : new FormControl('', Validators.required),
    cantidadCtrl : new FormControl('', Validators.required)
  });
  constructor(public service : BrmServiceService) { 
  }

  ngOnInit(): void {
    this.getCliente();
    this.getProducto();
  }

  getCliente(){
    this.service.getCliente().subscribe(
      async data => {
        if(data.success){
          this.clientes = data.data.clientes; // Llenar la data de  clientes
        }else{
          this.clientes = [];
        }
      }
    );
  }

  getProducto(){
    this.service.getProducto().subscribe(
      async data => {
        if(data.success){
          this.productos = data.data.productos; // Llenar la data de productos
        }else{
          this.productos = [];
        }
      }
    );
  }

  async onFormSubmit(){
    if(await this.verifyForm()){
      let pedido  = new Pedido();
      pedido.idCliente = this.clientesSelect;
      pedido.idProducto = this.productoSelect;
      pedido.cantidad = this.cantidadSelect;
      this.service.postPedido(pedido).subscribe(
        async data => {
          if(data.success){
            alert(data.messages[0]);
            this.limpiarForm();
          }else{
            alert(data.messages[0]);
          }
        }
      );
    }
  }

  setPrecio(){
    try{
      if(this.productoSelect != '' && this.cantidadSelect != ''){
        let proTmp = this.productos.find(element => <any> element.idProducto == this.productoSelect);
        if(proTmp != undefined){
          this.precio = proTmp.precioProducto * parseInt(this.cantidadSelect);
        }
      }else{
        this.precio = 0;
      }
    }catch(e){
      console.log('Error de conversión');
      this.precio = 0;
    }
  }

  verifyForm(){
    if(this.clientesSelect == ''){
      alert('El cliente es necesario');
      return false;
    }
    if(this.productoSelect == ''){
      alert('El pruducto es necesario');
      return false;
    }
    if(this.cantidadSelect == '' || this.cantidadSelect == '0'){
      alert('La cantidad es necesaria mayor a 0');
      return false;
    }
    return true;
  }

  limpiarForm(){
    this.clientesSelect = "";
    this.productoSelect = "";
    this.cantidadSelect = "";
  }
}
