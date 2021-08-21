import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { HttpClientModule } from '@angular/common/http';
import { ReactiveFormsModule } from '@angular/forms';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { FormCompraComponent } from './pages/form-compra/form-compra.component';
import { FormIngresoComponent } from './pages/form-ingreso/form-ingreso.component';
import { ViewInventarioComponent } from './pages/view-inventario/view-inventario.component';

@NgModule({
  declarations: [
    AppComponent,
    FormCompraComponent,
    FormIngresoComponent,
    ViewInventarioComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    HttpClientModule,
    ReactiveFormsModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
