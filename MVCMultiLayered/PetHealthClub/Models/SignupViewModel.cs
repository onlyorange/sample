using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Mvc;
using System.ComponentModel;
using System.ComponentModel.DataAnnotations;

namespace PetHealthClub.Models
{
    /// <summary>
    /// View model for the initial landing/signup page
    /// </summary>
    public class SignupViewModel
    {
        [Required(ErrorMessage = "*")]
        public string PetName { get; set; }
        [Required(ErrorMessage = "*"), Range(1,2)]
        public SelectList PetType { get; set; }
        
        //[Required(ErrorMessage = "*")]
        public string PetBreed { get; set; }
        //[Required(ErrorMessage = "*")]
        public int pcid { get; set; }
        
        public SignupViewModel()
        {
            PetType = new SelectList(new Dictionary<int, string>()
            {
                { 0, "Type" },    
                { 1, "Dog" },
                { 2, "Cat" }
            }
            , "key", "value" );
        }
    }

    /// <summary>
    /// Model used to post signup information back to the signup action
    /// </summary>
    public class SignupPostModel
    {
        public string PetName { get; set; }
        public int PetType { get; set; }
    }
}