import { Component } from '@angular/core';
import { HousingLocation } from '../housinglocation';
// import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-home',
  //standalone: true,
  //imports: [CommonModule],
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent {
  housingLocation: HousingLocation = {
    id: 999,
    name: 'Test Home',
    city: 'Edison',
    state: 'NJ',
    photo: 'assests/example-house.jpg',
    availableUnits: 99,
    wifi: true,
    laundry: false,
  };

}
