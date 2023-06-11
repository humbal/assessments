import { Component, Inject, inject } from '@angular/core';
import { HousingLocation } from '../housinglocation';
import { HousingService } from '../housing.service';
// import { CommonModule } from '@angular/common';
@Component({
  selector: 'app-home',
  //standalone: true,
  //imports: [CommonModule],
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent {
  // Assign housingLocationList value of empty array([]).
  housingLocationList: HousingLocation[] = [];
  // Inject DI
  housingService: HousingService = inject(HousingService);

  constructor() {
    this.housingLocationList = this.housingService.getAllHousingLocation();
  }
}
