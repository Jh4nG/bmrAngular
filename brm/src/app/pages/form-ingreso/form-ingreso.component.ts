import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { ProductoInv } from 'src/app/models/producto-inv';
import { BrmServiceService } from 'src/app/services/brm-service.service';


@Component({
  selector: 'app-form-ingreso',
  templateUrl: './form-ingreso.component.html',
  styleUrls: ['./form-ingreso.component.css']
})
export class FormIngresoComponent implements OnInit {
  nombreSelect = "";
  loteSelect = "";
  fechaSelect = "";
  cantidadSelect = "";
  precioSelect = "";
  
  formIngresoInv = new FormGroup({
    nombreCtrl : new FormControl('', Validators.required),
    loteCtrl : new FormControl('', Validators.required),
    fechaCtrl : new FormControl('', Validators.required),
    cantidadCtrl : new FormControl('', Validators.required),
    preciodCtrl : new FormControl('', Validators.required)
  });
  constructor(public service : BrmServiceService) { }

  ngOnInit(): void {
  }

  async onFormSubmit(){
    if(await this.verifyForm()){
      let productos = new ProductoInv;
      productos.nombreProducto = this.nombreSelect;
      productos.cantidadProducto = this.cantidadSelect;
      productos.loteProducto = this.loteSelect;
      productos.fechaProducto = this.fechaSelect;
      productos.precioProducto = this.precioSelect;
      this.service.setProducto(productos).subscribe(
        async data =>{
          if(data.success){
            alert(data.messages[0]);
            this.limpiarForm();
          }else{
            alert(data.messages[0]);
          }
        }
      )
    }
  }

  verifyForm(){
    if(this.nombreSelect == ''){
      alert('El nombre es necesario');
      return false;
    }
    if(this.loteSelect == ''){
      alert('El lote es necesario');
      return false;
    }
    if(this.fechaSelect == ''){
      alert('La fecha es necesaria');
      return false;
    }
    if(this.cantidadSelect == '' || this.cantidadSelect == '0'){
      alert('La cantidad es necesaria mayor a 0');
      return false;
    }
    if(this.precioSelect == '' || this.precioSelect == '0'){
      alert('El precio es necesaria mayor a 0');
      return false;
    }
    return true;
  }

  limpiarForm(){
    this.nombreSelect = "";
    this.loteSelect = "";
    this.fechaSelect = "";
    this.cantidadSelect = "";
    this.precioSelect = "";
  }

}
