import { TestBed } from '@angular/core/testing';

import { BrmServiceService } from './brm-service.service';

describe('BrmServiceService', () => {
  let service: BrmServiceService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(BrmServiceService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
