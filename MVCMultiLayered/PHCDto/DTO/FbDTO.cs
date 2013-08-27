using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.ComponentModel.DataAnnotations;

namespace PHCDto.DTO
{
    public class FbDTO
    {
        public string Email { get; set; }
        public string Fname { get; set; }
        public string Mname { get; set; }
        public string Lname { get; set; }
        public string Sex { get; set; }
        public string Address { get; set; }
        public string City { get; set; }
        public string State { get; set; }
        public string Country { get; set; }
        public string Zip { get; set; }
        public string Location { get; set; }
        public string Mobile { get; set; }
        public string About { get; set; }
        public string DOB { get; set; }
        public string Website { get; set; }
        public string Link { get; set; }

        public int UID { get; set; }
        public string fbid { get; set; }
        public string Username { get; set; }
        public string acctoken { get; set; }
        public int rcnt { get; set; }

        // Sharing trackers  
        public DateTime SharedToFacebookOn { get; set; }
        public DateTime SharedToTwitterOn { get; set; }
        public DateTime SharedToPinterestOn { get; set; }
    }
}
