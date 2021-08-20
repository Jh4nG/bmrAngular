import { HttpClient } from '@angular/common/http';
import { environment } from 'src/environments/environment';
import { Injectable } from '@angular/core';
import { Pedido } from '../models/pedido';

@Injectable({
  providedIn: 'root'
})
export class BrmServiceService {
  url : string = '';
  constructor(public http : HttpClient) {
    this.url = environment.endpoint;
  }

  getCliente(){
    return this.http.get<any>(this.url+'cliente');
  }

  getProducto(){
    return this.http.get<any>(this.url+'producto');
  }

  postPedido(pedido: Pedido){
    return this.http.post<any>(this.url+'pedido', pedido);
  }
}
