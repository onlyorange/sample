using System;
using System.Collections.Generic;
using System.Collections.Specialized;
using System.Linq;
using System.Web;
using System.Web.Mvc;

namespace PetHealthClub.Areas.Redemption.Models
{
    public class TestViewModel
    {
        public SelectList UserList { get; set; }
        public string FacebookId { get; set; }

        public SelectList PinList { get; set; }
        public string PinId { get; set; }

        public TestViewModel( List<PHCDto.DTO.FbDTO> users )
        {
            var items = new Dictionary<string, string>();
            foreach( var user in users )
            {
                items.Add( user.fbid, user.Fname + " " + user.Lname );
            }
            UserList = new SelectList( items, "Key", "Value" );
            PinList = new SelectList(new Dictionary<string, string>(){
                {"CTYWLTCP", "$3 Coupon"},
                {"GSLYKTCP", "$10 Coupon"},
                {"VIRTILDB", "Dog Bowl"},
                {"SXWLEADT", "Dog Tag"},
                {"JGJNNPGC", "$25 Gift Card"}
            }, "Key", "Value");
        }
    }

    public class TestPostModel
    {
        public string FacebookId { get; set; }
        public string PinId { get; set; }
    }
}