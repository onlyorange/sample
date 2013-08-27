using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using PHCData.Data;
using PHCDto.DTO;
using PHCRepos.Repos.Core;
using PHCRepos.Repos.AbstractContracts;

namespace PHCRepos.Repos.Core
{
    public class FbprofileDbProc : IFbprofileDbProc
    {
        PHCEntities context;

        public FbprofileDbProc(PHCEntities _phc)
        {
            context = _phc;
        }

        public override int usercount(FbDTO dto)
        {
            int cnt;
            using (var context = new PHCEntities())
            {
                cnt = context.Users.Where(p => p.fbid == dto.fbid).Count();
            }
            return cnt;
        }

        public override FbDTO Save_user(FbDTO dto)
        {

            using (var context = new PHCEntities())
            {
                FbDTO fb = new FbDTO();
                try
                {
                    User usr = new User();
                    usr.FirstName = dto.Fname;
                    usr.LastName = dto.Lname;
                    usr.Gender = dto.Sex;                  
                    usr.Email = dto.Email;
                    usr.fbid = dto.fbid;
                    usr.acesstoken = dto.acctoken;                 
                //    usr.Address = dto.Address;                 
                //    usr.Link = dto.Link;                 
                  
                    context.AddToUsers(usr);
                    context.SaveChanges();


                    fb.UID = usr.UID;
                    fb.Email = usr.Email;
                    fb.Fname = usr.FirstName;
                    fb.acctoken = usr.acesstoken;
                    fb.fbid = usr.fbid;
                }
                catch (Exception)
                {

                    throw;
                }
                return fb;
            }
        }


        public override FbDTO update_user(FbDTO dto)
        {
            using (var context = new PHCEntities())
            {
                FbDTO fb = new FbDTO();
                try
                {
                    User usr = context.Users.Where(p => p.fbid == dto.fbid).SingleOrDefault();
                    if (usr != null)
                    {
                        usr.FirstName = dto.Fname;
                        usr.LastName = dto.Lname;
                        usr.Gender = dto.Sex;
                        usr.Email = dto.Email;
                        usr.fbid = dto.fbid;
                        usr.acesstoken = dto.acctoken;

                     //   usr.Address = dto.Address;
                       
                    //    usr.Link = dto.Link;
                        

                        context.SaveChanges();
                    }


                    fb.UID = usr.UID;
                    fb.Email = usr.Email;
                    fb.Fname = usr.FirstName;
                    fb.acctoken = usr.acesstoken;
                    fb.fbid = usr.fbid;
                }
                catch (Exception)
                {

                    throw;
                }
                return fb;
            }
        }
        
        /// <summary>
        /// Updates a user and ensures the share fields are included
        /// </summary>
        /// <param name="dto"></param>
        public override void UserUpdateForShare(FbDTO dto)
        {
            using (var context = new PHCEntities())
            {
                User usr = context.Users.Where(p => p.fbid == dto.fbid).SingleOrDefault();
                if (usr != null)
                {
                    usr.FirstName = dto.Fname;
                    usr.LastName = dto.Lname;
                    usr.Gender = dto.Sex;
                    usr.Email = dto.Email;
                    usr.fbid = dto.fbid;
                    usr.acesstoken = dto.acctoken;
                    if( dto.SharedToFacebookOn > DateTime.MinValue )
                        usr.SharedToFacebookOn = dto.SharedToFacebookOn;
                    if (dto.SharedToPinterestOn > DateTime.MinValue)
                        usr.SharedToPinterestOn = dto.SharedToPinterestOn;
                    if (dto.SharedToTwitterOn > DateTime.MinValue)
                        usr.SharedToTwitterOn = dto.SharedToTwitterOn;

                    context.SaveChanges();
                }
            }
        }

        public override PetProfileDTO SavePetDetSignUp(PetProfileDTO dto)
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    PetProfileDTO pdto = new PetProfileDTO();
                    PetProfile pp = new PetProfile();
                    pp.UID = dto.UID;
                    pp.PetName = dto.PetName;
                    pp.PetBreed = dto.PetBreed;
                    pp.PetCategory = dto.pcid.ToString();
                    context.AddToPetProfiles(pp);
                    context.SaveChanges();
                    pdto.PID = pp.PID;
                    return pdto;
                }
                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }

        public override int UpdateUID(PetProfileDTO dt)
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    PetProfile pp =  context.PetProfiles.Where(p => p.PID == dt.PID).SingleOrDefault();
                    if (pp != null)
                    {
                        pp.UID = dt.UID;
                        context.SaveChanges();
                        int c = pp.PID;
                        return c;
                    }
                    else
                    {
                        return 0;
                    }
                    
                }
                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }

        /// <summary>
        /// Returns a single user by facebookId
        /// </summary>
        /// <param name="fbId"></param>
        /// <returns></returns>
        public override FbDTO GetUserByFacebookId(string fbId) {
            using (var context = new PHCEntities())
            {
                FbDTO fb = new FbDTO();
                var usr = context.Users.Where(p => p.fbid == fbId).SingleOrDefault();

                if (usr == null)
                    return fb;

                fb.UID = usr.UID;
                fb.Email = usr.Email;
                fb.Fname = usr.FirstName;
                fb.Lname = usr.LastName;
                fb.Sex = usr.Gender;
                fb.acctoken = usr.acesstoken;
                fb.fbid = usr.fbid;
                fb.SharedToFacebookOn = usr.SharedToFacebookOn.HasValue ? usr.SharedToFacebookOn.Value : DateTime.MinValue;
                fb.SharedToPinterestOn = usr.SharedToPinterestOn.HasValue ? usr.SharedToPinterestOn.Value : DateTime.MinValue;
                fb.SharedToTwitterOn = usr.SharedToTwitterOn.HasValue ? usr.SharedToTwitterOn.Value : DateTime.MinValue;

                return fb;
            }
        }

    }
}
