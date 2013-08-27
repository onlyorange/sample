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
    public class ChallengeDbProc : IChallengeDbProc
    {
        PHCEntities context;

        public ChallengeDbProc(PHCEntities _phc)
        {
            _phc = new PHCEntities();
        }

        #region Challenges

        public override int chalange_count(string cname)
        {
            int cnt = 0;
            using (var context = new PHCEntities())
            {
                try
                {
                    cnt = context.Challenges.Where(p => p.ChallengeName == cname).Count();
                }
                catch (Exception)
                {

                    throw;
                }

                return cnt;
            }
        }

        public override string Save_challenge(ChallengeDTO dto)
        {
            string msg = "";
            using (var context = new PHCEntities())
            {
                try
                {
                    Challenge ch = new Challenge();
                    ch.ChallengeName = dto.ChallengeName;
                    ch.Description = dto.Description;
                    ch.CatDescription = dto.CatDescription;
                    ch.DogChallengeImage = dto.dogchImg;
                    ch.CatChallengeImage = dto.catchImg;
                    ch.BadgeID = dto.BID;
                    ch.ChallengeCreatedDate = dto.ChcreatedDate;

                    context.AddToChallenges(ch);
                    context.SaveChanges();
                    msg = "success";
                }
                catch (Exception)
                {

                    msg = "fail";
                }
                return msg;
            }
        }

        public override ChallengeDTO ChkChallengebyDate(DateTime dt)
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    ChallengeDTO dto = new ChallengeDTO();
                    Challenge ch = context.Challenges.Where(u => u.ChallengeCreatedDate == dt).SingleOrDefault();
                    if (ch != null)
                    {
                        dto.CHID = ch.CHID;
                        dto.ChallengeName = ch.ChallengeName;
                        dto.Description = ch.Description;
                        dto.BID = Convert.ToInt32(ch.BadgeID);
                        dto.catchImg = ch.CatChallengeImage;
                        dto.dogchImg = ch.DogChallengeImage;
                        dto.CatDescription = ch.CatDescription;
                        return dto;
                    }
                    else
                    {
                        return null;
                    }
                }
                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }

        public override List<ChallengeDTO> Get_challenges_list()
        {
            using (var context = new PHCEntities())
            {
                List<ChallengeDTO> ldto = new List<ChallengeDTO>();
                try
                {
                    ChallengeDTO dto = null;
                    List<Challenge> lstch = new List<Challenge>();
                    lstch = context.Challenges.ToList();
                    if (lstch != null)
                    {
                        foreach (Challenge c in lstch)
                        {
                            dto = new ChallengeDTO();
                            dto.CHID = c.CHID;
                            dto.ChallengeName = c.ChallengeName;
                            dto.dogchImg = "~/Content/Uploads/Challenges/" + c.DogChallengeImage;
                            dto.catchImg = "~/Content/Uploads/Challenges/" + c.CatChallengeImage;
                            dto.Description = c.Description;
                            dto.SChcreatedDate = string.Format("{0:MM-dd/yyyy}", c.ChallengeCreatedDate);

                            dto.BID = Convert.ToInt32(c.BadgeID);
                            var badge = context.Badges.Where(p => p.BId == dto.BID).SingleOrDefault();
                            if (badge != null)
                            {
                                dto.bname = badge.Badgename;
                                dto.blogo = "~/Content/Uploads/Challenges/" + badge.Badgelogo;
                            }

                            ldto.Add(dto);
                        }
                    }
                }
                catch (Exception)
                {

                    throw;
                }
                return ldto;
            }
        }

        #endregion



        #region Badges

        public override int Badge_count(string bname)
        {
            int cnt = 0;
            using (var context = new PHCEntities())
            {
                try
                {
                    cnt = context.Badges.Where(p => p.Badgename == bname).Count();
                }
                catch (Exception)
                {

                    throw;
                }
                return cnt;
            }
        }

        public override List<badgeDTO> GetBadges_list()
        {
            using (var context = new PHCEntities())
            {
                List<badgeDTO> ldto = new List<badgeDTO>();
                badgeDTO dto = null;
                try
                {
                    List<Badge> bdg = new List<Badge>();
                    bdg = context.Badges.ToList();
                    if (bdg != null)
                    {
                        foreach (Badge b in bdg)
                        {
                            dto = new badgeDTO();
                            dto.BID = b.BId;
                            dto.Badgename = b.Badgename;
                            dto.badgeImage = "~/Content/Uploads/Challenges/" + b.Badgelogo;
                            dto.createdDate = string.Format("{0:MM-dd/yyyy}", b.createdDate);
                            ldto.Add(dto);
                        }
                    }
                }
                catch (Exception)
                {

                    throw;
                }
                return ldto;
            }
        }

        public override string Save_badge(badgeDTO dto)
        {
            string msg = "";
            using (var context = new PHCEntities())
            {
                try
                {
                    Badge bg = new Badge();
                    bg.Badgename = dto.Badgename;
                    bg.Badgelogo = dto.badgeImage;
                    bg.createdDate = DateTime.Now;
                    bg.CDescription = dto.cdescription;
                    bg.LDescription = dto.ldescription;

                    context.AddToBadges(bg);
                    context.SaveChanges();
                    msg = "success";
                }
                catch (Exception ex)
                {
                    msg = ex.Message;
                }
                return msg;
            }
        }

        public override List<ChallengeDTO> GetBadges()
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    List<ChallengeDTO> lstdto = new List<ChallengeDTO>();
                    ChallengeDTO dt = null;
                    List<Badge> lstb = context.Badges.ToList();
                    for (int i = 0; i < lstb.Count; i++)
                    {
                        dt = new ChallengeDTO();
                        dt.BID = lstb[i].BId;
                        dt.Description = lstb[i].Badgename;
                        lstdto.Add(dt);
                    }
                    return lstdto;
                }
                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }

        #endregion

        #region Users

        public override List<FbDTO> users_list()
        {
            using (var context = new PHCEntities())
            {
                List<FbDTO> ldto = new List<FbDTO>();
                FbDTO dto = null;
                try
                {
                    List<User> usr = new List<User>();
                    usr = context.Users.ToList();
                    if (usr != null)
                    {
                        foreach (User u in usr)
                        {
                            dto = new FbDTO();
                            dto.Fname = u.FirstName;
                            dto.Lname = u.LastName;
                            dto.Sex = u.Gender;
                            dto.Email = u.Email;
                            dto.UID = u.UID;
                            dto.fbid = u.fbid;
                            ldto.Add(dto);
                        }
                    }
                }
                catch (Exception)
                {

                    throw;
                }
                return ldto;
            }
        }

        #endregion


        public override string deleteuser(int id)
        {
            string msg = "";
            using (var context = new PHCEntities())
            {
                try
                {
                    User usr = context.Users.Where(p => p.UID == id).SingleOrDefault();

                    context.DeleteObject(usr);


                    List<PetEarnBadge> pern = context.PetEarnBadges.Where(k => k.UID == id).ToList();

                    if (pern != null)
                    {
                        foreach (PetEarnBadge c in pern)
                        {
                            context.DeleteObject(c);
                        }
                    }


                    List<PetProfile> profile = context.PetProfiles.Where(m => m.UID == id).ToList();

                    if (profile != null)
                    {
                        foreach (PetProfile n in profile)
                        {
                            context.DeleteObject(n);
                        }
                    }

                    context.SaveChanges();

                    msg = "success";
                }
                catch (Exception ex)
                {

                    msg = ex.Message;
                }
                return msg;
            }
        }



        public override badgeDTO Edit_badge(int id)
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    badgeDTO dto = new badgeDTO();

                    Badge badg = new Badge();
                    badg = context.Badges.Where(p => p.BId == id).SingleOrDefault();
                    if (badg != null)
                    {
                        dto.BID = badg.BId;
                        dto.Badgename = badg.Badgename;
                        dto.badgeImage = badg.Badgelogo;
                        dto.bnamehidden = badg.Badgename;
                        dto.cdescription = badg.CDescription;
                        dto.ldescription = badg.LDescription;
                    }

                    return dto;
                }
                catch (Exception)
                {

                    throw;
                }
            }
        }


        public override string Update_badge(badgeDTO dto)
        {
            string msg = "";
            using (var context = new PHCEntities())
            {
                try
                {
                    Badge bdg = new Badge();
                    bdg = context.Badges.Where(p => p.BId == dto.BID).SingleOrDefault();
                    if (bdg != null)
                    {
                        bdg.Badgename = dto.Badgename;
                        bdg.Badgelogo = dto.badgeImage;
                        bdg.createdDate = DateTime.Now;
                        bdg.CDescription = dto.cdescription;
                        bdg.LDescription = dto.ldescription;
                        context.SaveChanges();
                        msg = "success";
                    }
                }
                catch (Exception ex)
                {
                    msg = ex.Message;

                }
            }
            return msg;
        }


        public override string delete_badge(int id)
        {
            string msg = "";
            using (var context = new PHCEntities())
            {
                try
                {
                    Badge bdg = new Badge();
                    bdg = context.Badges.Where(p => p.BId == id).SingleOrDefault();
                    if (bdg != null)
                        context.DeleteObject(bdg);


                    List<Challenge> profile = context.Challenges.Where(m => m.BadgeID == id).ToList();

                    if (profile != null)
                    {
                        foreach (Challenge n in profile)
                        {
                            context.DeleteObject(n);
                        }
                    }

                    List<PetEarnBadge> pearn = context.PetEarnBadges.Where(m => m.BID == id).ToList();

                    if (pearn != null)
                    {
                        foreach (PetEarnBadge n in pearn)
                        {
                            context.DeleteObject(n);
                        }
                    }

                    context.SaveChanges();

                    msg = "success";
                }
                catch (Exception ex)
                {

                    msg = ex.Message;
                }
            }
            return msg;
        }


        public override string delete_challenge(int id)
        {
            string msg = "";
            using (var context = new PHCEntities())
            {
                try
                {
                    Challenge chl = new Challenge();
                    chl = context.Challenges.Where(p => p.CHID == id).SingleOrDefault();
                    if (chl != null)
                        context.DeleteObject(chl);

                    List<PetEarnBadge> pearn = context.PetEarnBadges.Where(m => m.CID == id).ToList();

                    if (pearn != null)
                    {
                        foreach (PetEarnBadge n in pearn)
                        {
                            context.DeleteObject(n);
                        }
                    }

                    msg = "success";

                    context.SaveChanges();
                }
                catch (Exception ex)
                {

                    msg = ex.Message;
                }
            }
            return msg;
        }

        public override ChallengeDTO Edit_challenge(int id)
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    ChallengeDTO adto = new ChallengeDTO();

                    Challenge chl = new Challenge();
                    chl = context.Challenges.Where(p => p.CHID == id).SingleOrDefault();

                    if (chl != null)
                    {
                        adto.CHID = chl.CHID;
                        adto.ChallengeName = chl.ChallengeName;
                        adto.Description = chl.Description;
                        adto.catchImg = chl.CatChallengeImage;
                        adto.dogchImg = chl.DogChallengeImage;
                        adto.ChcreatedDate = Convert.ToDateTime(chl.ChallengeCreatedDate);
                        string dob = null;
                        dob = string.Format("{0:MM/dd/yyyy}", adto.ChcreatedDate);
                        adto.SChcreatedDate = dob;
                        adto.BID = Convert.ToInt32(chl.BadgeID);
                        adto.chnamehidden = chl.ChallengeName;
                        adto.CatDescription = chl.CatDescription;
                    }

                    return adto;
                   
                }
                catch (Exception)
                {

                    throw;
                }
            }
        }


        public override string update_challenge(ChallengeDTO dto)
        {
            string msg = "";
            using (var context = new PHCEntities())
            {
                try
                {
                    Challenge chl = new Challenge();
                    chl = context.Challenges.Where(p => p.CHID == dto.CHID).SingleOrDefault();

                    if (chl != null)
                    {
                        chl.CHID = dto.CHID;
                        chl.ChallengeName = dto.ChallengeName;
                        chl.Description = dto.Description;
                        chl.BadgeID = dto.BID;
                        chl.DogChallengeImage = dto.dogchImg;
                        chl.CatChallengeImage = dto.catchImg;
                        chl.ChallengeCreatedDate =Convert.ToDateTime(dto.SChcreatedDate);
                        chl.CatDescription = dto.CatDescription;
                        context.SaveChanges();
                        msg = "success";
                    }
                }
                catch (Exception ex)
                {

                    msg = ex.Message;
                }
                return msg;
            }
        }


        public override string Save_Wallpaper(RewardsDTO adto)
        {
            string msg = "";
            using (var context = new PHCEntities())
            {
                try
                {
                    Wallpaper bg = new Wallpaper();
                    bg.Title = adto.Title;
                    bg.WallImage = adto.WallImage;
                   
                    context.AddToWallpapers(bg);
                    context.SaveChanges();
                    msg = "success";
                }
                catch (Exception ex)
                {
                    msg = ex.Message;
                }
                return msg;
            }
        }

        public override List<RewardsDTO> GetWall_list()
        {
            using (var context = new PHCEntities())
            {
                List<RewardsDTO> ldto = new List<RewardsDTO>();
                RewardsDTO dto = null;
                try
                {
                    List<Wallpaper> bdg = new List<Wallpaper>();
                    bdg = context.Wallpapers.ToList();
                    if (bdg != null)
                    {
                        foreach (Wallpaper b in bdg)
                        {
                            dto = new RewardsDTO();
                            dto.WID = b.WID;
                            dto.Title = b.Title;
                            dto.WallImage = "~/Content/Uploads/Challenges/" + b.WallImage;                          
                            ldto.Add(dto);
                        }
                    }
                }
                catch (Exception)
                {
                    throw;
                }
                return ldto;
            }
        }

        public override RewardsDTO Edit_Wall(int id)
        {
            using (var context = new PHCEntities())
            {
                try
                {
                    RewardsDTO dto = new RewardsDTO();

                    Wallpaper wall = new Wallpaper();
                    wall = context.Wallpapers.Where(p => p.WID == id).SingleOrDefault();
                    if (wall != null)
                    {
                        dto.WID = wall.WID;
                        dto.Title = wall.Title;
                        dto.WallImage = wall.WallImage;                      
                    }

                    return dto;
                }
                catch (Exception ex)
                {
                    throw ex;
                }
            }
        }

        public override string Update_Wall(RewardsDTO dto)
        {
            string msg = "";
            using (var context = new PHCEntities())
            {
                try
                {
                    Wallpaper wall = new Wallpaper();
                    wall = context.Wallpapers.Where(p => p.WID == dto.WID).SingleOrDefault();
                    if (wall != null)
                    {
                        wall.Title = dto.Title;
                        wall.WallImage = dto.WallImage;                     
                        context.SaveChanges();
                        msg = "success";
                    }
                }
                catch (Exception ex)
                {
                    msg = ex.Message;

                }
            }
            return msg;
        }

        public override string delete_wall(int id)
        {
            string msg = "";
            using (var context = new PHCEntities())
            {
                try
                {
                    Wallpaper wall = new Wallpaper();
                    wall = context.Wallpapers.Where(p => p.WID == id).SingleOrDefault();
                    if (wall != null)
                    {
                        context.DeleteObject(wall);
                        context.SaveChanges();
                        msg = "success";
                    }
                }
                catch (Exception ex)
                {
                    msg = ex.Message;
                }
            }
            return msg;
        }

    }
}
