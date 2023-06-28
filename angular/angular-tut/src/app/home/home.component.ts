import { Component, inject } from '@angular/core';
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

  filteredLocationList: HousingLocation[] = [];

  constructor() {
    /* this.housingLocationList = this.housingService.getAllHousingLocation();
    this.filteredLocationList = this.housingLocationList; */
    this.housingService.getAllHousingLocation().then((
      housingLocationList: HousingLocation[]) => {
        this.housingLocationList = housingLocationList;
        this.filteredLocationList = housingLocationList;
    });
  }

  filterResults(text: string) {
    if (!text) {
      this.filteredLocationList = this.housingLocationList;
    }

    this.filteredLocationList = this.housingLocationList.filter(
      housingLocation => housingLocation?.city.toLocaleLowerCase().includes(text.toLowerCase())
    );
  }
}
