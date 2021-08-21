import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ViewInventarioComponent } from './view-inventario.component';

describe('ViewInventarioComponent', () => {
  let component: ViewInventarioComponent;
  let fixture: ComponentFixture<ViewInventarioComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ViewInventarioComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(ViewInventarioComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
